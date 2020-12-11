<?php

namespace App\Http\Livewire\Admin\Carmu;

use App\Models\OldSystem\Customer;
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
          'transactionAmount' => 'required|numeric|min:1' . "|max:$balance",
          'paymentType' => ['required', 'string', Rule::in(['cash', 'transfer'])]
        ];
      } else {
        return [
          'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
          'transactionMoment' => ['required', 'string', Rule::in(['now', 'other'])],
          'transactionAmount' => 'required|numeric|min:1' . "|max:$balance",
          'paymentType' => ['required', 'string', Rule::in(['cash', 'transfer'])]
        ];
      }
    }

    return [
      'transactionType' => ['required', 'string', Rule::in(['credit', 'payment'])],
    ];
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
    DB::beginTransaction();
    try {
      //En primer lugar se recupera al cliente
      $customer = Customer::find($this->customerId);
      $processOk = false;

      if ($customer) {
        switch ($this->transactionType) {
          case 'credit':
            switch ($this->transactionMoment) {
              case 'now':
                $customer->credits()->create([
                  'description' => $this->description,
                  'amount' => $this->transactionAmount,
                ]);
                $processOk = true;
                break;
              case 'other':
                $customer->credits()->create([
                  'credit_date' => $this->transactionDate,
                  'description' => $this->description,
                  'amount' => $this->transactionAmount,
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
                  'cash' => $this->paymentType === 'cash' ? 1 : 0,
                  'amount' => $this->transactionAmount
                ]);
                $processOk = true;
                break;

              case 'other':
                $customer->payments()->create([
                  'cash' => $this->paymentType === 'cash' ? 1 : 0,
                  'payment_date' => $this->transactionDate,
                  'amount' => $this->transactionAmount
                ]);
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
        $this->reset('transactionType', 'transactionMoment', 'transactionDate', 'description', 'transactionAmount', 'paymentType');
      }

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      $this->emit('storeError');
    }
    // dd([
    //   'type' => $this->transactionType,
    //   'moment' => $this->transactionMoment,
    //   'date' => $this->transactionDate,
    //   'description' => $this->description,
    //   'amount' => $this->transactionAmount,
    //   'paymentType' => $this->paymentType
    // ]);
    // $this->transactionDate = "2020-12-01";
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
    $customer->balance = $this->getBalance($customer);
    $customer->balanceColor = $this->getBalanceColor($customer);
    $customer->state = $this->getState($customer);
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

      //Se recupera el saldo de la deuda del cliente
      $creditAmount = floatval($record->credits()->sum('amount'));
      $paymentAmount = floatval($record->payments()->sum('amount'));
      $balance = $creditAmount - $paymentAmount;

      //Se recuperan la fecha del ultimo abono
      $lastPayment = $record->payments()->orderBy('payment_date', 'desc')->first(['payment_date']);
      $lastPayment = $lastPayment ? Carbon::createFromFormat('Y-m-d H:i:s', $lastPayment->payment_date) : null;

      //Se recupera la fecha del primer credito
      $firstCredit = $record->credits()->orderBY('credit_date', 'asc')->first(['credit_date']);
      $firstCredit = $firstCredit ? Carbon::createFromFormat('Y-m-d H:i:s', $firstCredit->credit_date) : null;

      //Se recupera la fecha del ultimo credito
      $lastCredit = $record->credits()->orderBy('credit_date', 'desc')->first(['credit_date']);
      $lastCredit = $lastCredit ? Carbon::createFromFormat('Y-m-d H:i:s', $lastCredit->credit_date) : null;
      $lastCredit = $lastCredit 
                  ? 'Ultimo credito ' . $lastCredit->longRelativeToNowDiffForHumans()
                  : 'No tiene creditos';

      //Se definen las variables temporales
      $diffForHumans = "";
      $diffInDays = 0;
      $balanceColor = "text-success";
      $state = 'El cliente no tiene movimientos';

      //Se define el estado del saldo
      if ($balance > 0) {
        /**
         * Si el saldo es mayor que cero se presentan dos casos, 
         * en el primero de ellos el cliente ha realizado un abono
         * y en el segundo este no ha realizado ninguno.
         */
        if ($lastPayment) {
          $diffForHumans = $lastPayment->longRelativeToNowDiffForHumans();
          $diffInDays = $lastPayment->floatDiffInDays(Carbon::now());
          $state = "Ultimo abono $diffForHumans";
        } else {
          $diffForHumans = $firstCredit->longRelativeToNowDiffForHumans();
          $diffInDays = $firstCredit->floatDiffInDays(Carbon::now());
          $state = "Saldo pendiente $diffForHumans";
        }
      } else {
        /**
         * En este caso el cliente tambien presenta dos situaciones, el la primera
         * de ellas es que ya ha saldado su deuda y en el segundo caso simplemente
         * el cliente no tiene registrado ningun movimiento
         */
        if ($lastPayment) {
          $diffForHumans = $lastPayment->longRelativeToNowDiffForHumans();
          $diffInDays = $lastPayment->floatDiffInDays(Carbon::now());
          $state = "Deuda saldada $diffForHumans";
        } else {
          $state = "No tiene historial";
        }
      }

      /**
       * Ahora se define el color del saldo
       */
      if ($balance > 0) {
        if ($diffInDays > 30 && $diffInDays <= 45) {
          $balanceColor = "text-warning";
        } else if ($diffInDays > 45) {
          $balanceColor = "text-danger";
        }
      }

      $customer->balance = $balance;
      $customer->state = $state;
      $customer->lastCredit = $lastCredit;
      $customer->balanceColor = $balanceColor;
      $customer->time = $diffInDays;
      $customers->push((array) $customer);
    }

    $this->customers = $customers;
  }

  protected function getStateTwo($dataCredits, $dataPayments)
  {
    $credits = new Collection();      //Guarda los registro de los creditos pendientes
    $creditsPaid = new Collection();  //Guarda los registros de los creditos pagados
    $payments = new Collection();     //Guarda los registros de todos los abonos
    $timeOfPaid = new Collection();   //Guarda todos los tiempos de pago del cliente

    $balabce = 0;                     //Guarda el saldo pendiente del cliente
    $balanceColor = 'text-success';   //Guarda el estado del saldo del cliente
    $lastCredit = null;               
    $lastPayment = null;
    $state = 'No tiene movimientos';
    $time = 0;

    /**
     * Primero creo las entidades de los creditos y los pagos
     */
    if($dataCredits){
      foreach($dataCredits as $record){
        $credit = new stdClass();
        $credit->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->credit_date);
        $credit->amount = floatval($record->amount);
        $credit->balance = $credit->amount;
        $credits->push($credit);

        $balabce += $credit->amount;
      }
  
      foreach($dataPayments as $record){
        $payment = new stdClass();
        $payment->date = Carbon::createFromFormat('Y-md H:i:s', $record->payment_date);
        $payment->amount = floatval($record->amount);
        $payments->push($payment);
      }

      //Ahora se procede a pagar los creditos
      foreach($payments as $payment){
        $money = $payment->amount;

        while($money > 0){
          if($credits->first()->balance <= $money){
            $credit = $credits->shift();
            $money -= $credit->balance;
            $credit->balance = 0;
            //Se guarda el tiempo de duracion del pago
            $timeOfPaid->push($payment->date->diffInDays($credit->date));
          }else{
            
          }
        }

      }
    }

    
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

  protected function getBalanceColor($customer)
  {
    if ($customer->balance > 0) {
      if ($customer->lastPayment) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $customer->lastPayment->date);
      } else {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $customer->pendingCredits->first()->date);
      }

      $now = Carbon::now();
      $diff = $now->diffInDays($date);

      if ($diff <= 20) {
        return 'text-primary';
      } else if ($diff <= 30) {
        return 'text-warning';
      } else {
        return 'text-danger';
      }
    }
  }

  protected function getState($customer)
  {
    Carbon::setLocale('es_DO');
    if ($customer->balance > 0) {
      if ($customer->lastPayment) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $customer->lastPayment->date);
        // $diff = $date->diffForHumans(Carbon::now());
        $diff = $date->longRelativeToNowDiffForHumans();
        return "Ultimo abono $diff";
      } else {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $customer->pendingCredits->first()->date);
        // $diff = $date->diffForHumans(Carbon::now());
        $diff = $date->longRelativeToNowDiffForHumans();
        return "Pendiente $diff";
      }
    } else {
      if ($customer->lastPayment) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $customer->lastPayment->date);
        // $diff = $date->diffForHumans(Carbon::now());
        $diff = $date->longRelativeToNowDiffForHumans();
        return "Ultimo abono $diff";
      }

      return "No tiene transacciones";
    }
  }

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
