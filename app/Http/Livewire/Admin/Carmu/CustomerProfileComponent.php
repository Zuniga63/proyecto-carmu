<?php

namespace App\Http\Livewire\Admin\Carmu;

use App\Models\CashControl\Box;
use App\Models\OldSystem\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use stdClass;

class CustomerProfileComponent extends Component
{

  //-------------------------------------------------------------------------------
  //  VARIABLES CUANDO EL USUARIO ES SELECCIONADO
  //-------------------------------------------------------------------------------
  public $customerId = null;
  public $customer = null;

  //-------------------------------------------------------------------------------
  // VARIBLES DEL SISTEMA DE BUSQUEDA
  //-------------------------------------------------------------------------------
  public $search = "";
  public $customers = null;
  protected $queryString = ['search' => ['except' => '']];
  public $listingType = "active";

  //-------------------------------------------------------------------------------
  // FORMULARIO PARA NUEVAS TRANSACCIONES
  //-------------------------------------------------------------------------------
  public $transactionType = 'credit';
  public $transactionMoment = 'now';
  public $transactionDate = null;
  public $description = '';
  public $transactionAmount = '';
  public $paymentType = 'cash';

  protected $attributes = [
    'transactionType' => 'tipo de transacción',
    'transactionMoment' => 'now',
    'transactionDate' => 'fecha',
    'description' => 'descripción',
    'transactionAmount' => 'importe',
    'paymentType' => 'forma de pago'
  ];

  protected function rules()
  {
    if ($this->transactionType === 'credit') {
      if ($this->transactionMoment !== 'now') {
        return [
          'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
          'transactionMoment' => ['required', 'string', Rule::in(['now', 'other'])],
          'transactionDate' => [
            'nullable',
            'date',
            "after_or_equal:$this->minDate",
            "before_or_equal:$this->maxDate"
          ],
          'description' => 'required|string|max:255',
          'transactionAmount' => 'required|numeric|min:1',
        ];
      } else {
        return [
          'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
          'transactionMoment' => ['required', 'string', Rule::in(['now', 'other'])],
          'description' => 'required|string|max:255',
          'transactionAmount' => 'required|numeric|min:1',
        ];
      }
    } else {
      $balance = $this->customer['balance'];
      if ($this->transactionMoment !== 'now') {
        return [
          'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
          'transactionMoment' => ['required', 'string', Rule::in(['now', 'other'])],
          'transactionDate' => [
            'nullable',
            'date',
            "after_or_equal:$this->minDate",
            "before_or_equal:$this->maxDate"
          ],
          'transactionAmount' => 'required|numeric|min:1' . "|max:" . $this->getBalanceTwo(),
          'paymentType' => ['required', 'string', Rule::in(['cash', 'transfer'])]
        ];
      } else {
        return [
          'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
          'transactionMoment' => ['required', 'string', Rule::in(['now', 'other'])],
          'transactionAmount' => 'required|numeric|min:1' . "|max:" . $this->getBalanceTwo(),
          'paymentType' => ['required', 'string', Rule::in(['cash', 'transfer'])]
        ];
      }
    }

    return [
      'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
    ];
  }

  public function getTransactionsProperty()
  {
    $result = [];
    $customer = Customer::find($this->customerId);
    if ($customer) {
      if ($this->transactionType === 'credit') {
        $data = $customer->credits()->get();
        foreach ($data as $record) {
          $result[] = [
            'id' => $record->customer_credit_id,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $record->credit_date)->format('d-m-Y'),
            'description' => $record->description,
            'amount' => floatval($record->amount)
          ];
        }
      } else if ($this->transactionType === 'payment') {
        $data = $customer->payments()->get();
        foreach ($data as $record) {
          $result[] = [
            'id' => $record->customer_payment_id,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $record->payment_date)->format('d-m-Y'),
            'description' => $record->cash ? 'Pago en efectivo' : 'Transferencia',
            'amount' => floatval($record->amount)
          ];
        }
      }
    }

    return $result;
  }


  //-------------------------------------------------------------------------------
  //  VARIABLES COMPUTADAS
  //-------------------------------------------------------------------------------

  /**
   * Este metodo se encarga de acctualizar el listado de clientes
   * que se visualiza en el componente cuando no se ha seleccionado
   * a ningun cliente para su visualización
   */
  public function getCustomersListProperty()
  {
    $result = new Collection();

    if ($this->customers) {
      if (!empty(trim($this->search))) {
        $result = $this->customers->filter(function ($value, $key) {
          if ($this->listingType === 'active') {
            return Str::contains(Str::upper($value['fullName']), Str::upper($this->search))
              && $value['balance'] > 0
              && !$value['archived'];
          } else if ($this->listingType === 'archived') {
            return Str::contains(Str::upper($value['fullName']), Str::upper($this->search))
              && ($value['balance'] <= 0 || $value['archived']);
          }
          return true;
        });
      } else {
        $result = $this->customers->filter(function ($value, $key) {
          if ($this->listingType === 'active') {
            return $value['balance'] > 0 && !$value['archived'];
          } else if ($this->listingType === 'archived') {
            return $value['balance'] <= 0 || $value['archived'];
          }
          return true;
        });
      }

      return $result->sortBy('time');
    } else {
      return [];
    }
  }

  /**
   * Se encarga de establecer la fecha maxima del campo
   * de fecha del formulario de nueva transaccion
   * @return string fecha en formato YYYY-MM-DD
   */
  public function getMaxDateProperty()
  {
    return Carbon::now()->subDay()->format('Y-m-d');
  }

  /**
   * Este metodo se encarga de establecer la fecha minima en la que una transaccion se
   * debe ejecutar teneiendo en cuenta la fecha del primer credito pendiente
   * aunque si esta es menor que el año actual retorna esta
   * @return string Fecha en formato YYYY-MM-DD
   */
  public function getMinDateProperty()
  {
    $startOfYear = Carbon::now()->startOfYear();
    /**
     * Si el cliente tiene creditos pendientes
     * entonces la fecha minima es la fecha del primer 
     * credito pendiente
     */
    $pendingCredits = $this->customer['pendingCredits'];

    if (count($pendingCredits) > 0) {
      $date = Carbon::createFromFormat('d-m-y', $pendingCredits[0]['date'])->startOfDay();
      return $date->lessThanOrEqualTo($startOfYear) ? $startOfYear->format('Y-m-d') : $date->format('Y-m-d');
    }
    return $startOfYear->format('Y-m-d');
  }

  //------------------------------------------------------------------------------------
  // METODOS DEL SISTEMA DE TRANSACCIONES
  //------------------------------------------------------------------------------------

  /**
   * Almacena los datos de la nueva transacción en la base de datos 
   * y actualiza los datos del cliente
   */
  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);

    //Se procede a actualizar la base de datos
    try {
      DB::connection('carmu')->beginTransaction();
      DB::beginTransaction();

      //En primer lugar se recupera al cliente
      $customer = Customer::find($this->customerId);
      $processOk = false;
      $now = Carbon::now()->format('Y-m-d H:i:s');

      //Se recuperan las cajas de carmú
      /** @var Box */
      $localBox = Box::where('business_id', 1)->where('main', 1)->first();
      /** @var Box */
      $majorBox = Box::where('business_id', 1)->where('main', 0)->first();

      if ($customer && $localBox && $majorBox) {
        $customerName = trim("$customer->first_name $customer->last_name");
        switch ($this->transactionType) {
          case 'credit':
            switch ($this->transactionMoment) {
              case 'now':
                $customer->credits()->create([
                  'description'   => $this->description,
                  'amount'        => $this->transactionAmount,
                  'credit_date'  => $now,
                ]);

                //Se guarda el registro en la caja del local
                $localBox->transactions()->create([
                  'description' => "Venta a credito $customerName",
                  'type'        => 'sale',
                  'amount'      => $this->transactionAmount,
                  'transaction_date' => $now,
                ]);

                $localBox->transactions()->create([
                  'description' => "Credito al cliente $customerName",
                  'type'        => 'credit',
                  'amount'      => $this->transactionAmount * -1,
                  'transaction_date' => $now,
                ]);

                $processOk = true;
                break;
              case 'other':
                $customer->credits()->create([
                  'credit_date' => $this->transactionDate,
                  'description' => $this->description,
                  'amount' => $this->transactionAmount,
                ]);

                $date = Carbon::createFromFormat('Y-m-d', $this->transactionDate)
                  ->endOfDay()
                  ->format('Y-m-d H:i:s');

                $localBox->transactions()->create([
                  'transaction_date' => $date,
                  'description' => "Credito al cliente $customerName",
                  'type'        => 'credit',
                  'amount'      => $this->transactionAmount * -1,
                ]);

                $localBox->transactions()->create([
                  'transaction_date' => $date,
                  'description' => "Venta a credito $customerName",
                  'type'        => 'sale',
                  'amount'      => $this->transactionAmount,
                ]);
                $processOk = true;
                break;

              default:
                $this->emit('transationMomentError');
                break;
            }
            break;
          case 'payment':
            switch ($this->transactionMoment) {
              case 'now':
                $customer->payments()->create([
                  'cash'          => $this->paymentType === 'cash' ? 1 : 0,
                  'amount'        => $this->transactionAmount,
                  'payment_date'  => $now,
                ]);

                if ($this->paymentType === 'cash') {
                  $localBox->transactions()->create([
                    'description'       => "Abono del cliente $customerName",
                    'type'              => 'payment',
                    'amount'            => $this->transactionAmount,
                    'transaction_date'  => $now,
                  ]);
                } else {
                  $majorBox->transactions()->create([
                    'description'       => "Abono del cliente $customerName",
                    'type'              => 'payment',
                    'amount'            => $this->transactionAmount,
                    'transaction_date'  => $now,
                  ]);
                }
                $processOk = true;
                break;

              case 'other':
                $customer->payments()->create([
                  'cash' => $this->paymentType === 'cash' ? 1 : 0,
                  'payment_date' => $this->transactionDate,
                  'amount' => $this->transactionAmount
                ]);

                $date = Carbon::createFromFormat('Y-m-d', $this->transactionDate)
                  ->endOfDay()
                  ->format('Y-m-d H:i:s');

                if ($this->paymentType === 'cash') {
                  $localBox->transactions()->create([
                    'transaction_date'  => $date,
                    'description'       => "Abono del cliente $customerName",
                    'type'              => 'payment',
                    'amount'            => $this->transactionAmount,
                  ]);
                } else {
                  $majorBox->transactions()->create([
                    'transaction_date'  => $date,
                    'description'       => "Abono del cliente $customerName",
                    'type'              => 'payment',
                    'amount'            => $this->transactionAmount,
                  ]);
                }

                $processOk = true;
              default:
                $this->emit('transactionMomentError');
                break;
            }
            break;

          default:
            $this->emit('transactionTypeError');
            break;
        }
      } else {
        $this->emit('customerNotFound');
      }

      if ($processOk) {
        switch ($this->transactionType) {
          case 'credit':
            DB::connection('carmu')
              ->table('customer_history')
              ->insert([
                'user_id' => 2,
                'customer_id' => $this->customerId,
                'new_credit' => 1,
                'amount' => $this->transactionAmount
              ]);
            break;
          case 'payment':
            DB::connection('carmu')
              ->table('customer_history')
              ->insert([
                'user_id' => 2,
                'customer_id' => $this->customerId,
                'new_payment' => 1,
                'amount' => $this->transactionAmount
              ]);
            break;
          default:
            # code...
            break;
        }

        $this->loadCustomerData($this->customerId);
        $this->emit('transactionIsOk', $this->transactionType);
        $this->reset('transactionMoment', 'transactionDate', 'description', 'transactionAmount', 'paymentType');

        DB::commit();
        DB::connection('carmu')->commit();
      }
    } catch (\Throwable $th) {
      throw $th;
      $this->emit('storeError');
    }
  }

  /**
   * Este metodo utilizado por el administrador elimina los datos de un
   * credito o de un abono. 
   */
  public function destroyTransaction($id)
  {
    try {
      $customer = Customer::find($this->customerId);
      $userRol = User::find(auth()->user()->id)->roles()->orderBy('id')->first()->id;
      if ($customer && $userRol === 1) {
        $isOk = false;

        if ($this->transactionType === 'credit') {
          $customer->credits()->where('customer_credit_id', $id)->delete();
          $isOk = true;
        } else if ($this->transactionType === 'payment') {
          $customer->payments()->where('customer_payment_id', $id)->delete();
          $isOk = true;
        }

        if ($isOk) {
          $this->emit('transactionIsDeleted', $this->transactionType);
          $this->loadCustomerData($this->customerId);
        } else {
          $this->emit('storeError');
        }
      } else {
        $this->emit('storeError');
        $this->emit('customerNotFound');
      }
    } catch (\Throwable $th) {
      $this->emit('storeError');
    }
  }
  //------------------------------------------------------------------------------------
  // SISTEMA DE RENDERIZACIÓN
  //------------------------------------------------------------------------------------
  public function mount($id = null)
  {
    $this->fill(request()->only('search'));
    $this->customerId = $id;
    if ($id) {
      $this->loadCustomerData($id);
    } else {
      $this->loadCustomersData();
    }
  }

  /**
   * Se encarga de actualizar los datos del cliente
   */
  protected function loadCustomerData($id)
  {
    $data = Customer::findOrFail($id);
    $result = $data->getCreditHistory();

    $customer = new stdClass();

    //Se crean los datos del cliente
    $customer->id = $data->customer_id;
    $customer->firstName = $data->first_name;
    $customer->lastName = $data->last_name;
    $customer->fullName = $this->getFullName($customer);
    $customer->nit = $data->nit;
    $customer->phone = $data->phone;
    $customer->email = $data->email;
    $customer->good = $data->good_customer ? true : false;
    $customer->archived = $data->archived ? true : false;

    //Se asignan los parametros del historial
    $customer->history = $result['history'];
    $customer->pendingCredits = $result['pendingCredits'];
    $customer->expiredCredits = $result['expiredCredits'];
    $customer->creditsPaid = $result['creditsPaid'];
    $customer->lastCredit = $result['lastCredit'];
    $customer->lastPayment = $result['lastPayment'];
    $customer->successCredits = $result['successCredits'];

    //Se asigna el estado del cliente
    $credits = $data->credits()->orderBy('credit_date')->get();
    $payments = $data->payments()->orderBy('payment_date')->get();

    $statistics = $this->getState($credits, $payments);
    $customer->balance = $statistics->balance;
    $customer->balanceColor = $statistics->balanceState;
    $customer->state = $statistics->state;
    $customer->paymentStatistics = $this->getPaymentStatistics($customer);
    $customer->paymentStatisticsByTimeOfLive = $this->getPaymentStatisticsByTimeOfLive($customer);

    //Convierto las collecciones y objetos a arrays y transformo las fechas a datos string
    $customer->history->transform(function ($item, $key) {
      $item->date = $item->date->isoFormat('D-MM-YY');
      return (array) $item;
    });
    $customer->history->toArray();

    $customer->pendingCredits->transform(function ($item, $key) {
      $item->date = $item->date->format('d-m-y');
      $item->expiration = $item->expiration->shortRelativeToNowDiffForHumans();
      return (array) $item;
    });
    $customer->pendingCredits->toArray();

    $customer->expiredCredits->toArray();

    $customer->creditsPaid->transform(function ($item, $key) {
      $item->duration = $item->paymentDate->shortAbsoluteDiffForHumans($item->date);
      $item->paymentDate = $item->paymentDate->format('d-m-y');
      $item->date = $item->date->format('d-m-y');
      $item->expiration = $item->expiration->shortRelativeToNowDiffForHumans();
      return (array) $item;
    });
    $customer->creditsPaid->toArray();

    $customer->lastCredit = (array) $customer->lastCredit;

    $this->customer = (array)$customer;
  }

  /**
   * Este metodo se encarga de solicitar a la base de datos
   * toda las informacion relevante de los clientes para crear una vista rapida
   * para el usuario, actualiza la variable $customer
   */
  protected function loadCustomersData()
  {
    //Se recupera la informacion basica
    $attributes = ['customer_id', 'first_name', 'last_name', 'archived'];
    $data = Customer::orderBy('first_name')
      ->get($attributes);
    $customers = new Collection();

    //Ahora se recupera su estado
    foreach ($data as $record) {
      $customer = new stdClass();
      $customer->id = $record->customer_id;
      $customer->fullName = $record->first_name . ' ' . $record->last_name;
      $customer->archived = $record->archived == 0 ? false : true;

      //Recupero los creditos y abonos para calcular las estadisticas
      $credits = $record->credits()->orderBy('credit_date')->get();
      $payments = $record->payments()->orderBy('payment_date')->get();

      $statistics = $this->getState($credits, $payments);

      $customer->balance = $statistics->balance;
      $customer->state = $statistics->state;
      $customer->lastCredit = $statistics->lastCredit;
      $customer->balanceColor = $statistics->balanceState;
      $customer->time = $statistics->paymentTime;

      $customers->push((array) $customer);
    }

    $this->customers = $customers;
  }

  public function render()
  {
    return view('livewire.admin.carmu.customer-profile-component')
      ->layout("admin.carmu.customer-profile.index");
  }

  //-------------------------------------------------------------------------------
  // UTILIDADES
  //-------------------------------------------------------------------------------
  protected function getFullName($customer)
  {
    return $customer->firstName . " " . $customer->lastName;
  }

  protected function getBalance($customer)
  {
    $history = $customer->history;
    return $history->count() > 0 ? $history->last()->debt : 0;
  }

  protected function getBalanceTwo()
  {
    $data = Customer::find($this->customerId);

    if ($data) {
      return floatval($data->credits()->sum('amount')) - floatval($data->payments()->sum('amount'));
    }

    return 0;
  }

  /**
   * Este metodo se basa en la amortisacion de los creditos del cliente
   * para poder establecer el estado del mismo, el saldo y el estado del saldo
   */
  protected function getState($dataCredits, $dataPayments)
  {
    $credits = new Collection();          //Guarda los registro de los creditos pendientes
    $creditsPaid = new Collection();      //Guarda los registros de los creditos pagados
    $pendingCredits = new Collection();
    $payments = new Collection();         //Guarda los registros de todos los abonos

    $balance = 0;                         //Guarda el saldo pendiente del cliente
    $refDate = null;
    $refDates = new Collection();
    $balanceState = 'text-muted';        //Guarda el estado del saldo del cliente
    $paymentsTime = new Collection();     //Guarda todos los tiempos de pago del cliente
    $paymentTime = 0;
    $lastPayment = null;
    $lastCredit = null;
    $state = 'No tiene historial';
    $onlyCredits = true;

    /**
     * Primero creo las entidades de los creditos y los pagos
     */
    if ($dataCredits) {
      foreach ($dataCredits as $record) {
        $credit = new stdClass();
        $credit->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->credit_date)->startOfDay();
        $credit->amount = floatval($record->amount);
        $credit->balance = $credit->amount;
        $credits->push($credit);
      }

      foreach ($dataPayments as $record) {
        $payment = new stdClass();
        $payment->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->payment_date)->startOfDay();
        $payment->amount = floatval($record->amount);
        $payment->availableBalance = $payment->amount;
        $payments->push($payment);
      }

      /**
       * Se procede a amortizar los creditos
       */
    }

    while ($credits->count() > 0) {
      //Recupero los datos del primer credito
      $lastCredit = $credits->shift();
      //Se agrega a la lista de pendientes
      $pendingCredits->push($lastCredit);
      //Se verifican los datos del siguiente pago
      $nextPayment = $payments->count() > 0 ? $payments->first() : null;

      /**
       * Mientras existan pagos y el siguiente pago tenga una fecha menor 
       * que la fecha del actual credito
       */
      while ($nextPayment && $nextPayment->date->lessThanOrEqualTo($lastCredit->date)) {
        //Se recupera el primer credito pendiente
        $firstPendingCredit = $pendingCredits->count() > 0
          ? $pendingCredits->first()
          : null;

        $refDate = $nextPayment->date;
        $onlyCredits = false;

        /**
         * En este puento se supone que debe haber almenos una fecha 
         * pero por si acaso pongo la restricción
         */
        if ($refDates->count() > 0 && !$refDates->last()->equalTo($refDates)) {
          $refDates->push($refDate);
        }

        if ($firstPendingCredit) {
          if ($firstPendingCredit->balance <= $nextPayment->availableBalance) {
            //Se descuenta el saldo del credito del saldo del pago
            $nextPayment->availableBalance -= $firstPendingCredit->balance;
            $balance -= $firstPendingCredit->balance;
            //Se salda la cuenta del credito
            $firstPendingCredit->balance = 0;

            //Se retira de los creditos pendientes 
            $creditsPaid->push($pendingCredits->shift());
          } else {
            //Se decuenta el saldo del pago al credito
            $firstPendingCredit->balance -= $nextPayment->availableBalance;
            $balance -= $nextPayment->availableBalance;
            $nextPayment->availableBalance = 0;
          } //end if-else

          //Se establece la duracion de este pago
          // $time = $nextPayment->date->floatDiffInRealMonths($firstPendingCredit->date);
          $time = $nextPayment->date->floatDiffInRealMonths($refDates->get($refDates->count() - 2));
          if ($time > 0) {
            $paymentsTime->push($time);
          } //end if

          //Se verifica que todavía hay saldo en este pago
          if ($nextPayment->availableBalance <= 0) {
            $lastPayment = $payments->shift();
            $nextPayment = $payments->count() > 0
              ? $payments->first()
              : null;
          }
        } else {
          /**
           * En el caso de no encontrar creditos pendientes
           * para evitar un bucle infinito se rompe
           */
          break;
        } //end if
      } //end while

      //Se hace un check sobre el saldo del cliente esto es lo que
      //hace la magia y permite recordar si el cliente habia saldado su cuenta
      if ($balance <= 0) {
        $refDate = $lastCredit->date;
        $refDates->push($refDate);
        $onlyCredits = true;
      } //end if

      /**
       * Se actualiza la dueda del cliente
       */
      $balance += $lastCredit->amount;
    } //end while

    //Ahora se saldan los pagos pendientes
    while ($payments->count() > 0) {
      $nextPayment = $payments->shift();
      $refDate = $nextPayment->date;
      $onlyCredits = false;

      /**
       * En este puento se supone que debe haber almenos una fecha 
       * pero por si acaso pongo la restricción
       */
      if ($refDates->count() > 0 && !$refDates->last()->equalTo($refDates)) {
        $refDates->push($refDate);
      }

      do {
        $firstPendingCredit = $pendingCredits->count() > 0
          ? $pendingCredits->first()
          : null;

        if ($firstPendingCredit) {
          if ($firstPendingCredit->balance <= $nextPayment->availableBalance) {
            $nextPayment->availableBalance -= $firstPendingCredit->balance;
            $balance -= $firstPendingCredit->balance;
            $firstPendingCredit->balance = 0;
            $creditsPaid->push($pendingCredits->shift());
          } else {
            $firstPendingCredit->balance -= $nextPayment->availableBalance;
            $balance -= $nextPayment->availableBalance;
            $nextPayment->availableBalance = 0;
          }

          $time = $nextPayment->date->floatDiffInRealMonths($refDates->get($refDates->count() - 2));
          if ($time > 0) {
            $paymentsTime->push($time);
          } //end if
        }
      } while ($nextPayment->availableBalance > 0 && $firstPendingCredit);
    }


    if ($refDate) {
      //Por ultimo se agregan los tiempos de los creditos pendientes
      foreach ($pendingCredits as $credit) {
        $time = $refDate->floatDiffInRealMonths(Carbon::now());
        if ($time > 0) {
          $paymentsTime->push($time);
        } //end if
      }
      $diffFromNow = $refDate->longRelativeToNowDiffForHumans();
      $balanceDiff = $refDate->floatDiffInRealMonths(Carbon::now());
      $paymentTime = $paymentsTime->avg();
      if ($balance > 0) {
        if ($onlyCredits) {
          $state = "Saldo $diffFromNow";
        } else {
          $state = "Un abono $diffFromNow";
        } //end if-else

        $balanceState = $balanceDiff <= 0.8
          ? 'text-success'
          : ($balanceDiff <= 1.5  ? 'text-warning' : 'text-danger');
      } else {
        $state = "Deuda saldada $diffFromNow";
      } //end if-else
    }

    $lastCredit = $lastCredit
      ? 'Ultimo credito ' . $lastCredit->date->longRelativeToNowDiffForHumans()
      : 'No tiene creditos';

    $result = new stdClass();
    $result->balance = $balance;
    $result->refDate = $pendingCredits;
    $result->balanceState = $balanceState;
    // $result->pendingCredits = $pendingCredits->count();
    $result->paymentTime = $paymentTime;
    // $result->times = $paymentsTime;
    $result->lastCredit = $lastCredit;
    $result->state = $state;

    // dd($refDate, $refDates);
    return $result;
  } //end of method

  protected function getPaymentStatistics($customer)
  {
    $expiredCount = $customer->expiredCredits->count();
    $successCount = $customer->successCredits->count();
    $expiredAmount = 0;
    $successAmount = 0;
    $expiredAverage = 0;
    $successAverage = 0;
    $expiredWeightedAverage = 0;
    $successWeightedAverage = 0;
    $accumulated = 0;
    $count = $expiredCount + $successCount;

    foreach ($customer->expiredCredits as $credit) {
      $expiredAmount += floatval($credit->amount);
    }

    $accumulated += $expiredAmount;

    foreach ($customer->successCredits as $credit) {
      $successAmount += floatval($credit->amount);
    }

    $accumulated += $successAmount;


    if ($count > 0) {
      $expiredAverage = round($expiredCount / $count, 3);
      $successAverage = round($successCount / $count, 3);
      $expiredWeightedAverage = round($expiredAmount / $accumulated, 3);
      $successWeightedAverage = round($successAmount / $accumulated, 3);
    }

    return [
      'success' => [
        'count' => $successCount,
        'average' => $successAverage,
        'weighted' => $successWeightedAverage,
        'amount' => $successAmount
      ],
      'expired' => [
        'count' => $expiredCount,
        'average' => $expiredAverage,
        'weighted' => $expiredWeightedAverage,
        'amount' => $expiredAmount
      ],
      'total' => $count
    ];
  }

  protected function getPaymentStatisticsByTimeOfLive($customer)
  {
    $lessThanMonth = 0;
    $lessThanMonthWeighted = 0;
    $lessThanTwoMonths = 0;
    $lessThanTwoMonthsWeighted = 0;
    $lessThanThreeMonths = 0;
    $lessThanThreeMonthsWeighted = 0;
    $greaterThanThreeMonths = 0;
    $greaterThanThreeMonthsWeighted = 0;
    $accumulated = 0;

    foreach ($customer->creditsPaid as $credit) {
      $amount = floatval($credit->amount);
      $accumulated += $amount;
      $creditDate = Carbon::createFromFormat('Y-m-d H:i:s', $credit->date);
      $diff = $credit->paymentDate->floatDiffInMonths($creditDate);

      if ($diff <= 1) {
        $lessThanMonth++;
        $lessThanMonthWeighted += $amount;
      } else if ($diff <= 2) {
        $lessThanTwoMonths++;
        $lessThanTwoMonthsWeighted += $amount;
      } elseif ($diff <= 4) {
        $lessThanThreeMonths++;
        $lessThanThreeMonthsWeighted += $amount;
      } else {
        $greaterThanThreeMonths++;
        $greaterThanThreeMonthsWeighted += $amount;
      }
    }

    foreach ($customer->pendingCredits as $credit) {
      $amount = floatval($credit->amount);
      $accumulated += $amount;
      $creditDate = Carbon::createFromFormat('Y-m-d H:i:s', $credit->date);
      $diff = Carbon::now()->floatDiffInMonths($creditDate);

      if ($diff <= 1) {
        $lessThanMonth++;
        $lessThanMonthWeighted += $amount;
      } else if ($diff <= 2) {
        $lessThanTwoMonths++;
        $lessThanTwoMonthsWeighted += $amount;
      } elseif ($diff <= 4) {
        $lessThanThreeMonths++;
        $lessThanThreeMonthsWeighted += $amount;
      } else {
        $greaterThanThreeMonths++;
        $greaterThanThreeMonthsWeighted += $amount;
      }
    }

    if ($accumulated > 0) {
      $lessThanMonthWeighted = round(round($lessThanMonthWeighted / $accumulated, 2) * 100);
      $lessThanTwoMonthsWeighted = round(round($lessThanTwoMonthsWeighted / $accumulated, 2) * 100);
      $lessThanThreeMonthsWeighted = round(round($lessThanThreeMonthsWeighted / $accumulated, 2) * 100);
      $greaterThanThreeMonthsWeighted = round(round($greaterThanThreeMonthsWeighted / $accumulated, 2) * 100);
    }

    return [
      'oneMonth' => ['count' => $lessThanMonth, 'weighted' => $lessThanMonthWeighted],
      'twoMonths' => ['count' => $lessThanTwoMonths, 'weighted' => $lessThanTwoMonthsWeighted],
      'threeMonths' => ['count' => $lessThanThreeMonths, 'weighted' => $lessThanThreeMonthsWeighted],
      'moreThanThreeMonts' => ['count' => $greaterThanThreeMonths, 'weighted' => $greaterThanThreeMonthsWeighted]
    ];
  }
}
