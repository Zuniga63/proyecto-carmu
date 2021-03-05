<?php

namespace App\Http\Livewire\CashControl;

use App\Models\CashControl\Box;
use App\Models\CashControl\BoxTransaction;
use Carbon\Carbon;
use Error;
use Livewire\Component;

use function PHPUnit\Framework\throwException;

class ShowBoxs extends Component
{
  public ?array  $box           = null;
  public string  $state         = "registering";
  public string  $tab           = "info";
  public bool    $closingBox    = false;
  public ?int    $transactionId = null;

  //--------------------------------------
  // FORMULARIO DE NUEVA TRANSACCIÓN
  //--------------------------------------
  public string   $transactionType    = 'general';
  public string   $moment             = 'now';
  public string   $transactionDate    = '';
  public bool     $setTime            = false;
  public string   $transactionTime    = '';
  public string   $description        = '';
  public ?int     $transactionAmount  = 0;
  public string   $amountType         = 'income';

  //--------------------------------------
  // FORMULARIO DE CIERRE DE CAJA
  //--------------------------------------
  public ?int     $newBase            = null;
  public ?int     $destinationBox     = null;
  public ?int     $registeredCash     = null;
  public ?int     $missingCash        = null;
  public ?int     $leftoverCash       = null;
  public ?int     $cashReplenishment  = null;

  //--------------------------------------
  // REGLAS DE VALIDACIÓN
  //--------------------------------------
  protected function rules()
  {
    $rules = [];
    if ($this->closingBox) {
      //TODO: reglas para el cierre de caja
    } else {
      $rules = [
        'transactionType'   => 'required|string|in:general,sale,expense,purchase,service,credit,payment',
        'moment'            => 'required|string|in:now,other',
        'description'       => 'required|string|min:8,max:255',
        'transactionAmount' => 'required|integer|min:1000',
        'setTime'           => 'nullable|boolean'
      ];

      if ($this->transactionType === 'general') {
        $rules['amountType'] = 'required|string|in:income,expense';
      }

      if ($this->moment !== 'now') {
        $rules['transactionDate'] = "required|string|date|after_or_equal:$this->minDate|before_or_equal:$this->maxDate";
        if ($this->setTime) {
          $rules['transactionTime'] = 'required|string|date_format:H:i';
        }
      }
    }

    return $rules;
  }

  //--------------------------------------
  // PROPIEDADES COMPUTADAS
  //--------------------------------------
  /**
   * Recupera los datos de todas las
   * cajas de la base de datos y las guarda en
   * cache
   */
  public function getBoxsProperty()
  {
    $boxs = [];
    if (empty($this->box)) {
      $boxData = Box::orderBy('id')->with(['business', 'cashier'])->get();

      foreach ($boxData as $data) {
        $boxs[] = $this->getBoxInfo($data);
      }
    }

    return $boxs;
  }

  public function getMinDateProperty()
  {
    if ($this->box) {
      return Carbon::createFromFormat('Y-m-d H:i:s', $this->box['closingDate'])->format('Y-m-d');
    }

    return '';
  }

  /**
   * La fecha maxima que es un dia anterior al actual o
   * en su defecto el día de hoy si el cierre es hoy
   */
  public function getMaxDateProperty()
  {
    if ($this->state === 'editing') {
      return Carbon::now()->format('Y-m-d');
    }
    return Carbon::now()->subDay()->format('Y-m-d');
  }

  public function getTransactionsProperty()
  {
    $transactions = [];
    if ($this->box) {
      $boxId       = $this->box['id'];
      $closingDate = $this->box['closingDate'];
      $balance = 0;
      $isoFormat = 'DD-MM-YYYY';
      $dateTimeFormat = 'Y-m-d H:i:s';
      /** @var Box */
      $box = Box::find($boxId);
      $balance = round($box->transactions()->where('transaction_date', '<', $closingDate)->sum('amount'));

      //Ahora se recuperan todas las demas transacciones
      $data = $box->transactions()
        ->orderBy('created_at')
        ->where('transaction_date', '>=', $closingDate)
        ->get();

      foreach ($data as $record) {
        $date = Carbon::createFromFormat($dateTimeFormat, $record->transaction_date)->isoFormat($isoFormat);
        $amount = round($record->amount);
        $balance = $balance + $amount;

        $transactions[] = [
          'id'          => $record->id,
          'date'        => $date,
          'description' => $record->description,
          'amount'      => $amount,
          'balance'     => $balance,
        ];
      }
    }
    return $transactions;
  }

  //----------------------------------------------------------------
  // RENDERIZACIÓN
  //----------------------------------------------------------------
  public function render()
  {
    $this->emit('updateAmount', abs($this->transactionAmount));
    return view('livewire.cash-control.show-boxs')->layout('livewire.cash-control.show-box.index');
  }

  public function mount(?int $id = null)
  {
    if ($id) {
      $box = Box::find($id);
      if ($box) {
        $this->boxId = $id;
        $this->box   = $this->getBoxInfo($box);
      } else {
        $this->redirect(route('admin.showBox'));
      }
    }
  }

  //-----------------------------------------------------------------
  // UTILIDADES GENERALES
  //-----------------------------------------------------------------
  protected function emitError($th)
  {
    $title = '¡Ups, Algo salió mal!';
    $message = "Contacte con el administrador!";
    $type = 'error';
    $this->alert($title, $message, $type);
    if (env('APP_DEBUG')) {
      throw $th;
    }
  }

  protected function alert(?string $title = null, ?string $type = 'warning', ?string $message = null)
  {
    $this->emit('alert', $title, $message, $type);
  }

  protected function doesNotPermission(string $action)
  {
    $title = "¡Acción denegada!";
    $message = "No tiene el permiso para $action";
    $type = 'error';
    $this->alert($title, $type, $message);
  }

  public function resetFields()
  {
    $this->reset('state', 'transactionId', 'closingBox', 'transactionType', 'moment', 'description', 'transactionAmount', 'amountType', 'newBase', 'destinationBox', 'registeredCash', 'missingCash', 'leftoverCash', 'cashReplenishment');
    $this->emit('reset');
  }

  //-----------------------------------------------------------------
  // UTILIDADES DEL COMPONENTE
  //-----------------------------------------------------------------
  /**
   * Recupera la informacion de una caja
   */
  protected function getBoxInfo(Box $box)
  {
    $transactionTypes = [
      'general'   => ['income' => 0, 'expense' => 0],
      'sale'      => ['income' => 0, 'expense' => 0],
      'expense'   => ['income' => 0, 'expense' => 0],
      'purchase'  => ['income' => 0, 'expense' => 0],
      'service'   => ['income' => 0, 'expense' => 0],
      'credit'    => ['income' => 0, 'expense' => 0],
      'payment'   => ['income' => 0, 'expense' => 0]
    ];

    $business     = $box->business ? $box->business->name : 'Negocio no asignado';
    $cashier      = $box->cashier ? $box->cashier->name : 'Cajero no asignado';
    $closingDate  = $box->closing_date;
    $originDate   = $closingDate;
    $base         = round($box->base);
    $sales = $services = $payments = $otherIncomes = $incomesAmount = 0;
    $expenses = $purchase = $credits = $otherExpenses = $expensesAmount = 0;
    $calBalance = $base;
    $transactionsCount = 0;

    //Se consulta el numero de transacciones para limintar las consultas
    $transactionsCount = $box->transactions()->where('transaction_date', '>=', $closingDate)->count();

    //Se consultan los ingresos por ventas
    foreach ($transactionTypes as $type => $result) {
      if ($transactionsCount > 0) {
        $result['income'] = $this->getBoxTypeAmount($type, $box, $transactionsCount);
        $result['expense'] = $this->getBoxTypeAmount($type, $box, $transactionsCount, false);
      }
      $transactionTypes[$type] = $result;
    }

    $sales = $transactionTypes['sale']['income'];
    $services = $transactionTypes['service']['income'];
    $payments = $transactionTypes['payment']['income'];
    $otherIncomes = $transactionTypes['general']['income'];
    $incomesAmount = $sales + $services + $payments + $otherIncomes;

    $expenses = $transactionTypes['expense']['expense'];
    $purchase = $transactionTypes['purchase']['expense'];
    $credits = $transactionTypes['credit']['expense'];
    $otherExpenses = $transactionTypes['general']['expense'];
    $expensesAmount = $expenses + $purchase + $credits + $otherExpenses;

    $calBalance += $incomesAmount + $expensesAmount;
    $isoClosingDate = Carbon::createFromFormat('Y-m-d H:i:s', $closingDate)
      ->isoFormat('MMMM Do YYYY, h:mm:ss a');

    return [
      'id'              => $box->id,
      'name'            => $box->name,
      'isoClosingDate'  => $isoClosingDate,
      'closingDate'     => $closingDate,
      'base'            => $base,
      'business'        => $business,
      'cashier'         => $cashier,
      'sales'           => $sales,
      'services'        => $services,
      'payments'        => $payments,
      'otherIncomes'    => $otherIncomes,
      'incomesAmount'   => $incomesAmount,
      'expenses'        => $expenses,
      'purchases'       => $purchase,
      'credits'         => $credits,
      'otherExpenses'   => $otherExpenses,
      'expensesAmount'  => $expensesAmount,
      'balance'         => $calBalance,
    ];
  }

  /**
   * Se encarga de recupear el importe de las transacciones que cumple con el tipo
   * @param string $transactionType Tipo de transacción de las siete posible
   * @param Box $box Instancia del modelo de caja
   * @param int $globalCount Es el contador global de transacciones
   * @param bool $income True para recuperar ingresos, false para recuperar egresos
   * @return float Suma de todas las transacciones que cumplen las condicones
   */
  protected function getBoxTypeAmount(string $transactionType, Box $box, int &$globalCount, bool $income = true)
  {
    $localCount = 0;
    $closingDate = $box->closing_date;
    $amount = 0;

    /** Se construye la consulta */
    $query = $box->transactions()->where('transaction_date', '>=', $closingDate);

    /**
     * Se definesi se van a recuperar solo los
     * ingresos o los egresos
     */
    if ($income) {
      $query->where('amount', '>', 0);
    } else {
      $query->where('amount', '<', 0);
    }

    switch ($transactionType) {
      case 'general':
        $query->where('type', 'general');
        break;
      case 'sale':
        $query->where('type', 'sale');
        break;
      case 'expense':
        $query->where('type', 'expense');
        break;
      case 'purchase':
        $query->where('type', 'purchase');
        break;
      case 'service':
        $query->where('type', 'service');
        break;
      case 'credit':
        $query->where('type', 'credit');
        break;
      case 'payment':
        $query->where('type', 'payment');
        break;
      default:
        throw new Error("Tipo de transaccion no soportado");
        break;
    }

    $localCount = $query->count();
    if ($localCount > 0) {
      $amount = round($query->sum('amount'));
      $globalCount -= $localCount;
    }

    return $amount;
  }

  //-------------------------------------------
  // MANIPULACIÓN DE DATOS
  //-------------------------------------------
  protected function storeTransaction()
  {
    //Se inicializan las variables a utilizar
    $moment       = $this->moment;
    $dateIsOk     = true;                     //Una ultima validación de la fecha
    $closingDate  = $this->box['closingDate'];
    $date         = $this->transactionDate;
    $setTime      = $this->setTime;
    $time         = $this->transactionTime;
    $description  = $this->description;
    $type         = $this->transactionType;
    $amount       = $this->transactionAmount;
    $amountType   = $this->amountType;
    $inputs       = [];                       //Para guardar los datos a ingresar

    //Se inicializan las variables de la alerta
    $alertTitle = null;
    $alertType = 'error';
    $alertMessage = null;

    //Se recupera la instancia de la caja
    /** @var Box */
    $box = Box::find($this->box['id']);

    if ($box) {
      //Se guardan los campos obligatorios
      $inputs['type'] = $type;
      $inputs['description'] = $description;

      //Se define el signo del importe
      switch ($type) {
        case 'general':
          $amount = $amountType === 'income' ? $amount : $amount * -1;
          break;
        case 'expense':
        case 'purchase':
        case 'credit':
          $amount = $amount * -1;
          break;
      }
      //Se guarda el campo
      $inputs['amount'] = $amount;

      //Se establece la fecha
      if ($moment !== 'now') {
        if ($setTime) {
          $date = "$date $time";
          $date = Carbon::createFromFormat('Y-m-d H:i', $date);
        } else {
          $date = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
        }

        $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $closingDate);
        //Se valida que la fecha no sea menor que la fecha de cierre de caja
        if ($closingDate->lessThan($date)) {
          //Se agrega el campo
          $inputs['transaction_date'] = $date->format('Y-m-d H:i:s');
        } else {
          $dateIsOk = false;
          $alertTitle = "¡Fecha anterior al corte!";
          $alertMessage = "La fecha de la transacción es ";
          $alertMessage .= $date->longRelativeDiffForHumans($closingDate);
        }
      }

      if ($dateIsOk) {
        //Se procede a registrar la transacción
        $box->transactions()->create($inputs);
        $alertTitle = "Transacción Registrada";
        $alertType = 'success';
        $this->resetFields();
        $this->box = $this->getBoxInfo($box);
      }
    } else {
      $alertTitle = "¡Caja no encontrada!";
      $alertMessage = "Es probable que esta caja no esté en la base de datos";
      $alertType = 'warning';
    }

    $this->alert($alertTitle, $alertType, $alertMessage);
  }

  protected function updateTransaction()
  {
    /** @var BoxTransaction */
    $transaction  = BoxTransaction::find($this->transactionId);
    $moment       = $this->moment;
    $dateIsOk     = true;
    $closingDate  = $this->box['closingDate'];
    $date         = $this->transactionDate;
    $setTime      = $this->setTime;
    $time         = $this->transactionTime;
    $description  = $this->description;
    $type         = $this->transactionType;
    $amount       = $this->transactionAmount;
    $amountType   = $this->amountType;

    //Se inicializan las variables de la alerta
    $alertTitle = null;
    $alertType = 'error';
    $alertMessage = null;

    if ($transaction) {
      //Se actualizan los campos genericos
      $transaction->type = $type;
      $transaction->description = $description;

      //Se define el signo del importe
      switch ($type) {
        case 'general':
          $amount = $amountType === 'income' ? $amount : $amount * -1;
          break;
        case 'expense':
        case 'purchase':
        case 'credit':
          $amount = $amount * -1;
          break;
      }

      //Se actualiza el campo del importe
      $transaction->amount = $amount;

      //Ahora se establece la fecha
      if ($moment !== 'now') {
        if ($setTime) {
          $date = "$date $time";
          $date = Carbon::createFromFormat('Y-m-d H:i', $date);
        } else {
          $date = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
        }

        $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $closingDate);
        $now = Carbon::now();
        /**
         * Se valida que la fecha no sea anterior a la
         * fecha de cierre o posterior al momento actual
         */
        if ($closingDate->lessThan($date)) {
          if ($now->greaterThan($date)) {
            $transaction->transaction_date = $date->format('Y-m-d H:i:s');
          } else {
            $dateIsOk = false;
            $alertTitle = "¡Error con la fecha!";
            $alertMessage = "La fecha de la transacción es mayor que la fecha actual ";
            $alertMessage .= $date->longRelativeToNowDiffForHumans();
          }
        } else {
          $dateIsOk = false;
          $alertTitle = "!Error con la fecha!";
          $alertMessage = "La fecha de la transacción es anterior a la fecha de cierre de la caja ";
          $alertMessage .= $date->longRelativeDiffForHumans($closingDate);
        }

        if ($dateIsOk) {
          $transaction->save();
          $alertTitle = "Transacción Actualizada";
          $alertType = 'success';
          $this->resetFields();
          $box = $transaction->box()->first();
          $this->box = $this->getBoxInfo($box);
        }
      } else {
        $transaction->transaction_date = Carbon::now()->format('Y-m-d H:i:s');
        $transaction->save();
        $alertTitle = "Transacción Actualizada";
        $alertType = 'success';
        $this->resetFields();
        $box = $transaction->box()->first();
        $this->box = $this->getBoxInfo($box);
      }
    } else {
      $alertTitle = "¡Transacción eliminada";
    }

    $this->alert($alertTitle, $alertType, $alertMessage);
  }

  protected function storeClosingBox()
  {
    //TODO
  }

  public function editTransaction($id)
  {
    $transaction = BoxTransaction::find($id);
    if ($transaction) {
      $date = Carbon::createFromFormat('Y-m-d H:i:s', $transaction->transaction_date);
      $amount = round($transaction->amount);

      $this->transactionId = $transaction->id;
      $this->state = 'editing';
      $this->transactionType = $transaction->type;
      $this->moment = 'other';
      $this->transactionDate = $date->format('Y-m-d');
      $this->setTime = true;
      $this->transactionTime = $date->format('H:i');
      $this->description = $transaction->description;
      $this->amountType = $amount >= 0 ? 'income' : 'expense';
      $this->transactionAmount = abs($amount);
      $this->emit('updateAmount', abs($amount));
    } else {
      $this->alert('¡Transacción no encontrada!');
    }
  }

  public function destroyTransaction($id)
  {
    //TODO
  }

  public function submit()
  {
    $this->validate($this->rules(), []);
    try {
      if ($this->closingBox) {
        $this->storeClosingBox();
      } elseif ($this->state === 'registering') {
        $this->storeTransaction();
      } else {
        $this->updateTransaction();
      }
    } catch (\Throwable $th) {
      $this->emitError($th);
    }
  }
}
