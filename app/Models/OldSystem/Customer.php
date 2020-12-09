<?php

namespace App\Models\OldSystem;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Collection;
use stdClass;

class Customer extends Model
{
  use HasFactory;
  protected $table = "customer";
  protected $primaryKey = "customer_id";
  public $timestamps = false;
  protected $connection = 'carmu';
  protected $fillable = ['first_name', 'last_name', 'nit', 'phone', 'email', 'good_customer'];
  protected $guarded = ['id'];

  public function credits()
  {
    return $this->hasMany(CustomerCredit::class, 'customer_id');
  }

  public function payments()
  {
    return $this->hasMany(CustomerPayment::class, 'customer_id');
  }

  public function getCreditHistory()
  {
    // Las siguientes son las variables de resultado
    $creditHistory = new Collection();
    $pendingCredits = new Collection();
    $creditsPaid = new Collection();
    $expiredCredits = new Collection();
    $successCredits = new Collection();
    $lastCredit = null;
    $lastPayment = null;

    //Se recuperan los creditos y los abonos
    $credits = $this->credits()->orderBy('credit_date')->get(['credit_date as date', 'description', 'amount']);
    $payments = $this->payments()->orderBy('payment_date')->get(['payment_date as date', 'amount']);

    $nextPaymentDate = $payments->count() > 0
        ? Carbon::createFromFormat('Y-m-d H:i:s', $payments->first()->date)->startOfDay()
        : null;

    /**
     * Se recorren los credtos para ir creando los registros
     * del historial, definiendo los creditos pendientes y los creditos
     * pagados.
     */
    foreach ($credits as $credit) {
      $creditRegistered = false;              //Variable utilizada para evitar que se dupliquen los creditos
      $actualCredit = new stdClass();
      $actualCredit->date = Carbon::createFromFormat('Y-m-d H:i:s', $credit->date)->startOfDay();
      $actualCredit->paymentDate = null;
      $actualCredit->expiration = $actualCredit->date->copy()->addMonth($this->calculateTerm($credit->amount));
      $actualCredit->description = $credit->description;
      $actualCredit->amount = floatval($credit->amount);
      $actualCredit->balance = floatval($credit->amount);

      //Se actualiza la variable de memoria
      $lastCredit = $actualCredit;

      //Se agrega el credito a la lista de creditos pendientes
      $pendingCredits->push($actualCredit);

      /**
       * El siguiente codigo se debe repetir 
       * hasta que la fecha del siguiente pago
       * sea mayor que la fecha del actual credito 
       * o en su defecto sea un valor null
       */
      while ($nextPaymentDate && $nextPaymentDate->lessThanOrEqualTo($actualCredit->date)) {
        //Se recupera la información del pago, retirandolo de la lista.
        $lastPayment = $payments->shift();

        //Se cancela la deuda pendiente de los creditos actualmente activos
        $this->payPendingCredits($nextPaymentDate, $lastPayment->amount, $pendingCredits, $creditsPaid);

        //Se verifica en que espacio temporal se encuentra el pago
        if ($nextPaymentDate->lessThan($actualCredit->date)) {
          //Solo se procesa el pago
          $this->processTransaction(
            $nextPaymentDate,
            null,
            $lastPayment->amount,
            $creditRegistered ? $pendingCredits->count() : $pendingCredits->count() - 1,
            $creditHistory
          );
        } else {
          //Se procesa el credito si este no ha sido registrado
          if (!$creditRegistered) {
            $this->processTransaction(
              $actualCredit->date,
              $credit->amount,
              null,
              $pendingCredits->count(),
              $creditHistory
            );

            $creditRegistered = true;
          }

          //Se procesa el pago
          $this->processTransaction(
            $nextPaymentDate,
            null,
            $lastPayment->amount,
            $pendingCredits->count(),
            $creditHistory
          );
        }

        //Se actualiza la fecha del siguiente credito
        $nextPaymentDate = $payments->count() > 0
          ? Carbon::createFromFormat('Y-m-d H:i:s', $payments->first()->date)->startOfDay()
          : null;
      } //End while

      /**
       * Se registra el pago solo si no ha sido procesado en el bucle anterior 
       * o si directamente se lo ha saltado
       */
      if (!$creditRegistered) {
        $this->processTransaction(
          $actualCredit->date,
          $credit->amount,
          null,
          $pendingCredits->count(),
          $creditHistory
        );
      }
    } // end foreach

    /**
     * Antes de proceder se verifica si exsiten pagos pendientes
     */
    while ($nextPaymentDate) {
      //Se recupera la información del pago, retirandolo de la lista.
      $lastPayment = $payments->shift();

      //Se cancela la deuda pendiente de los creditos actualmente activos
      $this->payPendingCredits($nextPaymentDate, $lastPayment->amount, $pendingCredits, $creditsPaid);

      //Solo se procesa el pago
      $this->processTransaction(
        $nextPaymentDate,
        null,
        $lastPayment->amount,
        $creditRegistered ? $pendingCredits->count() : $pendingCredits->count() - 1,
        $creditHistory
      );

      //Se actualiza la fecha del siguiente credito
      $nextPaymentDate = $payments->count() > 0
      ? Carbon::createFromFormat('Y-m-d H:i:s', $payments->first()->date)->startOfDay()
      : null;
    }

    /**
     * Terminado el pocesamiento del historial se definen  los creditos vencidos
     * tanto de la lista de los creditos pagados como de la lista de los creditos
     * pendientes
     */
    foreach ($creditsPaid as $credit) {
      if ($credit->expiration->lessThan($credit->paymentDate)) {
        $expiredCredits->push($credit);
      }else{
        $successCredits->push($credit);
      }
    }

    foreach ($pendingCredits as $credit) {
      if ($credit->expiration->lessThan(Carbon::now())) {
        $expiredCredits->push($credit);
      }else{
        $successCredits->push($credit);
      }
    }

    $result = [
      'history' => $creditHistory,
      'pendingCredits' => $pendingCredits,
      'expiredCredits' => $expiredCredits,
      'creditsPaid' => $creditsPaid,
      'successCredits' => $successCredits,
      'lastCredit' => $lastCredit,
      'lastPayment' => $lastPayment
    ];
    return $result;
  } // end of method

  /**
   * Realiza todas las validacoiones correspondientes y luego crea un registro para el historial
   * @param Carbon $date Corresponde a la fecha de la transaccion para el historial en Y-m-d
   * @param float $credit Es el valor del credito a registrar
   * @param float $payment Es el valor del abono realizado por el cliente
   * @param int $pendingCredits Es el numero de creditos pendientes al momento de la transaccion
   * @param Collection $creditHistory es el arreglo con todas las transacciones
   */
  protected function processTransaction(Carbon $date, $credit, $payment, int $pendingCredits, Collection $creditHistory)
  {
    $credit = $credit ? floatval($credit) : null;
    $payment = $payment ? floatval($payment) : null;
    $date->startOfDay();
    if ($creditHistory->count() > 0) {
      //Se recupera el ultimo registro del historial
      $lastRecord = $creditHistory->last();

      /**
       * El flujo se divide en dos, en la primera parte modifica el
       * ultimo registro del historia.
       * En la segunda parte se procede a crear un nuevo registro
       */
      if ($lastRecord->date->equalTo($date)) {
        /**
         * En este punto se tienen tres posibles escenarios
         */
        if ($credit && $payment) {
          $lastRecord->credit += $credit;
          $lastRecord->payment += $payment;
          $lastRecord->debt += $credit - $payment;
        } else if ($credit) {
          $lastRecord->credit += $credit;
          $lastRecord->debt += $credit;
        } else {
          $lastRecord->payment += $payment;
          $lastRecord->debt -= $payment;
        } //End if-else-if-else
      } else {
        /**
         * En este punto tambien existen tres escenarios
         */
        if ($credit && $payment) {
          $debt = $lastRecord->debt + $credit - $payment;
          $creditHistory->push($this->createNewHistoryRecord($date, $credit, $payment, $debt, $pendingCredits));
        } else if ($credit) {
          $debt = $lastRecord->debt + $credit;
          $creditHistory->push($this->createNewHistoryRecord($date, $credit, null, $debt, $pendingCredits));
        } else {
          $debt = $lastRecord->debt - $payment;
          $creditHistory->push($this->createNewHistoryRecord($date, null, $payment, $debt, $pendingCredits));
        } //End if-else-if-else

      } //End if-else
    } else {
      if ($credit && $payment) {
        $debt = $credit - $payment;
        $creditHistory->push($this->createNewHistoryRecord($date, $credit, $payment, $debt, $pendingCredits));
      } else {
        $creditHistory->push($this->createNewHistoryRecord($date, $credit, null, $credit, $pendingCredits));
      } //end of if-else
    } //End if-else
  } //end function

  /**
   * Convierte los datos en un objeto stdClass
   * @param string $date Fecha en formato Y-m-d
   * @param float $credit Valor del credito por defecto null
   * @param float $payment El valor del abono realizado por el cliente
   * @param float $debt Es el saldo de la deuda al momento de la transaccion
   * @param int $pendingCredits El numero de creditos pendientes al momento de la transaccion
   */
  protected function createNewHistoryRecord($date, $credit = null, $payment = null, $debt = null, $pendingCredits = null)
  {
    $record = new stdClass();
    $record->date = $date;
    $record->credit = $credit;
    $record->payment = $payment;
    $record->debt = $debt;
    $record->pendingCredits = $pendingCredits;
    return $record;
  }

  /**
   * Se encarga de definir el plazo de los creditos basandose en 
   * el valor del cupo.
   */
  protected function calculateTerm($creditAmount)
  {
    $creditAmount = floatval($creditAmount);
    $maximumQuota = 250000;
    $termBase = 2;
    $framentation = $maximumQuota / $termBase;
    if ($creditAmount > $maximumQuota) {
      $diff = ceil(($creditAmount - $maximumQuota) / $framentation);
      $diff = $diff <= 4 ? $diff : 4;
      return $termBase + $diff;
    }

    return $termBase;
  }

  /**
   * Este metodo se encarga de ir saldando los creditos pendientes segun el importe del pago
   * y de actualizar los datos de los creditos pagados
   * @param Carbon $paymentDate Es la fecha en la que se realiza el pago
   * @param float $paymentAmount Es el importe del pago
   * @param Collection $pendingCredits Instancia de Collection con los pagos pendientes
   * @param Collection $creditsPaid Instancia de Collection con los pagos ya realizados
   */
  protected function payPendingCredits(Carbon $paymentDate, $paymentAmount, Collection $pendingCredits, Collection $creditsPaid)
  {
    $paymentAmount = floatval($paymentAmount);
    while ($pendingCredits->count() > 0 && $paymentAmount > 0) {
      if ($pendingCredits->first()->balance <= $paymentAmount) {
        $credit = $pendingCredits->shift();
        $paymentAmount -= $credit->balance;
        $credit->balance = 0;
        $credit->paymentDate = $paymentDate->copy();
        $creditsPaid->push($credit);
      } else {
        $pendingCredits->first()->balance -= $paymentAmount;
        $paymentAmount = 0;
      }
    } //end While
  } //end method
}
