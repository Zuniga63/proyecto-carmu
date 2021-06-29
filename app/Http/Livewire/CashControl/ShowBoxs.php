<?php

namespace App\Http\Livewire\CashControl;

use App\Models\CashControl\Box;
use App\Models\CashControl\BoxTransaction;
use App\Models\CashControl\Business;
use App\Models\User;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

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
  public ?string     $password           = null;
  public ?int        $boxBalance         = 0;
  public ?int        $newBase            = 0;
  public ?int        $destinationBox     = 0;
  public ?int        $registeredCash     = 0;
  public ?int        $missingCash        = 0;
  public ?int        $leftoverCash       = 0;
  public ?int        $cashReplenishment  = 0;

  //--------------------------------------
  // REGLAS DE VALIDACIÓN
  //--------------------------------------
  protected function rules()
  {
    $rules = [];
    if ($this->closingBox) {
      $rules = [
        'password'        => 'required|string',
        'newBase'         => 'required|integer|min:0',
        'registeredCash'  => 'required|integer|min:0',
      ];
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

  /**
   * Construye las reglas de validación en función de los datos suministrados 
   * por el frontend
   */
  protected function transactionRules($data)
  {
    $rules = [
      'boxId' => 'required|numeric|exists:box,id',
      'moment' => 'required|string|in:now,other',
      'setTime' => 'required|bool',
      'type' => 'required|string|in:general,sale,expense,purchase,service,credit,payment',
      'description' => 'required|string|min:3,max:255',
      'amount' => 'required|integer|min:100|max:100000000',
    ];

    if ($data['type'] === 'general') {
      $rules['amountType'] = 'required|string|in:income,expense';
    }

    if ($data['moment'] !== 'now') {
      //Se recupera la fecha de corte de la caja
      $box = Box::find($data['boxId'], ['closing_date']);
      if ($box) {
        $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $box->closing_date)->format('Y-m-d');
        $now = Carbon::now()->format('Y-m-d');

        $rules['date'] = "required|string|date|after_or_equal:$closingDate|before_or_equal:$now";

        if ($data['setTime']) {
          $rules['time'] = 'required|string|date_format:H:i';
        }
      }
    }

    if (array_key_exists('transactionId', $data)) {
      $rules['transactionId'] = 'required|integer|exists:box_transaction,id';
    }

    return $rules;
  }

  /**
   * Nombres personalizados de los atributos de la validación
   */
  protected $transactionAttributes = [
    'boxId' => 'Identificador de la caja',
    'moment' => 'Momento de la transacción',
    'type' => 'Tipo de transacción',
    'description' => 'Descripción',
    'amount' => 'Importe de la transacción',
    'date' => 'Fecha',
    'time' => 'Hora',
    'amountType' => 'Tipo de importe',
    'transactionId' => 'Identificador de la transacción',
  ];

  protected function closingBoxRules()
  {
    $rules = [
      'boxId' => 'required|integer|exists:box,id',
      'cashRegister' => 'required|integer|min:100|max:100000000',
      'newBase' => 'required|integer|min:100|max:100000000'
    ];

    return $rules;
  }

  protected $closingBxAttributes = [
    'boxId' => 'Identificador de la caja',
    'cashRegister' => 'arqueo',
    'newBase' => 'nueva base'
  ];

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

  public function getTransactionTypesProperty()
  {
    return [
      'general'   => 'Generales',
      'sale'      => 'Ventas',
      'expense'   => 'Gastos',
      'purchase'  => 'Compras',
      'service'   => 'Servicios',
      'credit'    => 'Creditos',
      'payment'   => 'Abonos',
      'transfer'  => 'Transferencias',
    ];
  }

  public function getBoxs()
  {
    $boxs = [];
    $data = Box::orderBy('id')->with(['business', 'cashier', 'transactions' => function ($query) {
      $query->orderBy('transaction_date')->orderBy('amount', 'DESC');
    }])->get();

    foreach ($data as $record) {
      $boxs[] = $this->buildBox($record);
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
    $this->emit('updateAmount', abs($this->transactionAmount), $this->newBase);
    return view('livewire.cash-control.show-boxs')->layout('livewire.cash-control.show-box.index');
  }

  public function mount(?int $id = null)
  {
    if ($id) {
      $box = Box::find($id);
      if ($box) {
        $this->boxId = $id;
        $this->newBase = intval($box->base);
        $this->box   = $this->getBoxInfo($box);
      } else {
        $this->redirect(route('admin.showBox'));
      }
    }
  }

  public function init()
  {
    return [
      'business' => $this->getBusiness(),
      'boxs' => $this->getBoxs(),
      'transactionTypes' => $this->getTransactionTypesProperty()
    ];
  }

  public function getBusiness()
  {
    $businnes = [];
    $data = Business::orderBy('id')->get(['id', 'name']);
    foreach ($data as $shop) {
      $businnes[] = [
        'id' => intval($shop->id),
        'uuid' => uniqid('business-'),
        'name' => $shop->name,
      ];
    }
    return $businnes;
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
      'payment'   => ['income' => 0, 'expense' => 0],
      'transfer'  => ['income' => 0, 'expense' => 0],
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
    $transfersIncomes = $transactionTypes['transfer']['income'];
    $otherIncomes = $transactionTypes['general']['income'];
    $incomesAmount = $sales + $services + $payments + $transfersIncomes + $otherIncomes;

    $expenses = $transactionTypes['expense']['expense'];
    $purchase = $transactionTypes['purchase']['expense'];
    $credits = $transactionTypes['credit']['expense'];
    $transfersExpenses = $transactionTypes['transfer']['expense'];
    $otherExpenses = $transactionTypes['general']['expense'];
    $expensesAmount = $expenses + $purchase + $credits + $transfersExpenses + $otherExpenses;

    $calBalance += $incomesAmount + $expensesAmount;
    $isoClosingDate = Carbon::createFromFormat('Y-m-d H:i:s', $closingDate)
      ->isoFormat('MMMM Do YYYY, h:mm:ss a');

    $this->boxBalance = $calBalance;
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
      'deposits'        => $transfersIncomes,
      'otherIncomes'    => $otherIncomes,
      'incomesAmount'   => $incomesAmount,
      'expenses'        => $expenses,
      'purchases'       => $purchase,
      'credits'         => $credits,
      'transfers'        => $transfersExpenses,
      'otherExpenses'   => $otherExpenses,
      'expensesAmount'  => $expensesAmount,
      'balance'         => $calBalance,
    ];
  }

  protected function buildBox(Box $box)
  {
    $business     = $box->business ? $box->business->name : 'Negocio no asignado';
    $cashier      = $box->cashier ? $box->cashier->name : 'Cajero no asignado';
    $transactions = [];

    foreach ($box->transactions as $transaction) {
      $transactions[] = [
        'id' => $transaction->id,
        'date' => $transaction->transaction_date,
        'type' => $transaction->type,
        'description' => $transaction->description,
        'amount' => intval($transaction->amount),
        'createdAt' => $transaction->created_at,
        'updatedAt' => $transaction->updated_at,
      ];
    }

    return [
      'id' => intval($box->id),
      'name' => $box->name,
      'main' => $box->main ? true : false,
      'closingDate' => $box->closing_date,
      'base' => intval($box->base),
      'business' => $business,
      'cashier' => $cashier,
      'transactions' => $transactions
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
      case 'transfer':
        $query->where('type', 'transfer');
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
  public function storeTransaction($data)
  {
    $ok = false;            //Determina si el proceso fue correctamente
    $errors = null;         //Guarda los errores de la validación
    $log = [];
    $transaction = null;    //Guarda la isntancia de la transacción que fue creada
    $dateIsOk = true;

    $rules = $this->transactionRules($data);
    $attributes = $this->transactionAttributes;

    try {
      $inputs = Validator::make($data, $rules, [], $attributes)->validate();

      //Se recupera la caja 
      /** @var Box */
      $box = Box::find($inputs['boxId'], ['id', 'name', 'closing_date as closingDate']);

      //Se construyen los campos a guardar
      $type = $inputs['type'];
      $description = $inputs['description'];
      $amount = intval($inputs['amount']);
      $date = Carbon::now();

      //Se establece la fecha si fue ingresada manualmente
      if ($inputs['moment'] === 'other') {
        $log['moment'] = 'La fecha se ingresa de forma manual';
        //Se crean las varibales temporales
        $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $box->closingDate);
        $now = Carbon::now();

        //Se verifica si la hora tambien fue manual
        if ($inputs['setTime']) {
          $log['time'] = 'La hora se ingresa de forma manual';
          $fullDate = $inputs['date'] . ' ' . $inputs['time'];
          //Se modifica la instancia de fecha
          $date = Carbon::createFromFormat('Y-m-d H:i', $fullDate);
        } else {
          //Se crea el objeto con solo date
          $date = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
          $log['onlyDate'] = $date->format('Y-m-d H:i:s');
          //Se verifica que fuera el mismo día del cierre, en cuyo caso sería la misma un segundo mastarde
          if ($date->day === $closingDate->day && $date->month === $closingDate->month && $date->year === $closingDate->year) {

            $date = $closingDate->copy()->addSecond();
          }
        }

        if (!$date->greaterThanOrEqualTo($closingDate) && !$date->lessThanOrEqualTo($now)) {
          $dateIsOk = false;
        }
      }

      //Se define el signo del importe
      switch ($type) {
        case 'general':
          $amount = $inputs['amountType'] === 'income' ? $amount : $amount * -1;
          break;
        case 'expense':
        case 'purchase':
        case 'credit':
          $amount = $amount * -1;
          break;
      }

      //Se procede a guardar la transacción
      if ($dateIsOk) {
        $transaction = $box->transactions()->create([
          'transaction_date' => $date->format('Y-m-d H:i:s'),
          'description' => $description,
          'type' => $type,
          'amount' => $amount
        ]);

        $ok = true;
        $transaction = [
          'boxId' => intval($box->id),
          'id' => intval($transaction->id),
          'type' => $transaction->type,
          'date' => $transaction->transaction_date,
          'description' => $transaction->description,
          'amount' => $transaction->amount,
          'createdAt' => $transaction->created_at,
          'updatedAt' => $transaction->updated_at,
        ];
        $this->alert('Transacción Guardada', 'success');
      } else {
        $this->alert('Fecha Incorrecta', 'error');
      }
    } catch (ValidationException $valExc) {
      $errors = $valExc->errors();
    } catch (\Throwable $th) {
      $this->emitError($th);
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'log' => $log,
      'transaction' => $transaction,
    ];
  }

  public function updateTransaction($data)
  {
    $ok = false;            //Determina si el proceso fue correctamente
    $errors = null;         //Guarda los errores de la validación
    $log = [];
    $transaction = null;    //Guarda la isntancia de la transacción que fue creada
    $dateIsOk = true;

    $rules = $this->transactionRules($data);

    $attributes = $this->transactionAttributes;

    try {
      $inputs = Validator::make($data, $rules, [], $attributes)->validate();

      //Se recupera la caja 
      /** @var Box */
      $box = Box::find($inputs['boxId'], ['id', 'name', 'closing_date as closingDate']);

      //Se construyen los campos a guardar
      $type = $inputs['type'];
      $description = $inputs['description'];
      $amount = intval($inputs['amount']);
      $date = Carbon::now();

      //Se establece la fecha si fue ingresada manualmente
      if ($inputs['moment'] === 'other') {
        $log['moment'] = 'La fecha se ingresa de forma manual';
        //Se crean las varibales temporales
        $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $box->closingDate);
        $now = Carbon::now();

        //Se verifica si la hora tambien fue manual
        if ($inputs['setTime']) {
          $log['time'] = 'La hora se ingresa de forma manual';
          $fullDate = $inputs['date'] . ' ' . $inputs['time'];
          //Se modifica la instancia de fecha
          $date = Carbon::createFromFormat('Y-m-d H:i', $fullDate);
        } else {
          //Se crea el objeto con solo date
          $date = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
          $log['onlyDate'] = $date->format('Y-m-d H:i:s');
          //Se verifica que fuera el mismo día del cierre, en cuyo caso sería la misma un segundo mastarde
          if ($date->day === $closingDate->day && $date->month === $closingDate->month && $date->year === $closingDate->year) {

            $date = $closingDate->copy()->addSecond();
          }
        }

        if (!$date->greaterThanOrEqualTo($closingDate) && !$date->lessThanOrEqualTo($now)) {
          $dateIsOk = false;
        }
      }

      //Se define el signo del importe
      switch ($type) {
        case 'general':
          $amount = $inputs['amountType'] === 'income' ? $amount : $amount * -1;
          break;
        case 'expense':
        case 'purchase':
        case 'credit':
          $amount = $amount * -1;
          break;
      }

      //Se procede a guardar la transacción
      if ($dateIsOk) {
        //Se recupera la tranacción
        $transaction = BoxTransaction::find($inputs['transactionId']);
        if ($transaction) {
          $transaction->transaction_date = $date->format('Y-m-d H:i:s');
          $transaction->description = $description;
          $transaction->type = $type;
          $transaction->amount = $amount;
          $transaction->save();

          $ok = true;
          $transaction = [
            'boxId' => intval($box->id),
            'id' => intval($transaction->id),
            'type' => $transaction->type,
            'date' => $transaction->transaction_date,
            'description' => $transaction->description,
            'amount' => $transaction->amount,
            'createdAt' => $transaction->created_at,
            'updatedAt' => $transaction->updated_at,
          ];
          $this->alert('Transacción Actualizada', 'info');
        } else {
          $this->alert('Transacción no encontrada', 'error');
        }
      } else {
        $this->alert('Fecha Incorrecta', 'error');
      }
    } catch (ValidationException $valExc) {
      $errors = $valExc->errors();
    } catch (\Throwable $th) {
      $this->emitError($th);
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'log' => $log,
      'transaction' => $transaction,
    ];
  }

  public function setPassword(string $password)
  {
    $this->password = $password;
  }

  public function storeClosingBox($data)
  {
    $ok = false;            //Determina si el proceso fue correctamente
    $errors = null;         //Guarda los errores de la validación
    $log = [];

    $rules = $this->closingBoxRules();
    $attributes = $this->closingBxAttributes;

    try {
      $inputs = Validator::make($data, $rules, [], $attributes)->validate();

      //Se crean las variables del cierre
      $now = Carbon::now();
      $cash = $inputs['cashRegister'];
      $newBase = $inputs['newBase'];

      //Se recupera la caja y el saldo
      /** @var Box */
      $box = Box::find($inputs['boxId']);
      $boxBalance = intval($box->transactions()->sum('amount'));

      /**
       * Caja principal o secundaria
       * @var Box
       */
      $majorBox = Box::where('id', '!=', $box->id)
        ->where('business_id', $box->business_id)
        ->orderBy('id')
        ->first();

      //Se incia transacción segura 
      DB::beginTransaction();

      //Se comprueba que la caja permita el cierre
      if ($box->main && $majorBox) {
        /** Fecha para transacciones adicionales */
        $date = $now->copy()->subSecond()->format('Y-m-d H:i:s');

        //Se registra el faltante o el sobrante un segundo antes del cierre
        if ($cash != $boxBalance) {
          $diff = $cash - $boxBalance;
          $description = $diff > 0 ? 'Sobrante de caja.' : 'Faltante de caja.';

          //Se registra la transacción
          $box->transactions()->create([
            'transaction_date' => $date,
            'type' => 'general',
            'description' => $description,
            'amount' => $diff
          ]);
        }

        //Se hace la transferencia de dinero si es requerido
        if ($newBase != $cash) {
          $diff = $newBase - $cash;

          $description = $diff > 0 ? 'Deposito de caja mayor.' : 'Transferencia a caja mayor.';

          //Caja menor
          $box->transactions()->create([
            'transaction_date' => $date,
            'type' => 'transfer',
            'description' => $description,
            'amount' => $diff
          ]);

          //Caja principal
          $description = $diff > 0 ? 'Transferencia a caja mejor.' : 'Cierre de caja menor.';
          $majorBox->transactions()->create([
            'transaction_date' => $date,
            'type' => 'transfer',
            'description' => $description,
            'amount' => $diff * -1
          ]);
        }

        //Ahora se actualiza la caja correspondiente
        $box->base = $newBase;
        $box->closing_date = $now->format('Y-m-d H:i:s');
        $box->save();

        //Se finaliza la transacción
        DB::commit();
        $ok = true;
        $this->alert('La caja ha sido cerrada', 'success');
      } else {
        $this->alert('¡Funcionalidad no habilitada!', 'Esta caja no tiene habilitado el cierre...');
      }
    } catch (ValidationException $valExc) {
      $errors = $valExc->errors();
    } catch (\Throwable $th) {
      $this->emitError($th);
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'log' => $log
    ];
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

  // public function submit()
  // {
  //   $this->validate($this->rules(), []);
  //   try {
  //     if ($this->closingBox) {
  //       $this->storeClosingBox();
  //     } elseif ($this->state === 'registering') {
  //       $this->storeTransaction();
  //     } else {
  //       $this->updateTransaction();
  //     }
  //   } catch (\Throwable $th) {
  //     $this->emitError($th);
  //   }
  // }
}
