<?php

namespace App\Http\Livewire\CashControl;

use App\Models\CashControl\Box;
use App\Models\CashControl\BoxTransaction;
use App\Models\CashControl\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ShowBoxs extends Component
{

  //--------------------------------------
  // REGLAS DE VALIDACIÓN
  //--------------------------------------
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

  protected $closingBoxAttributes = [
    'boxId' => 'Identificador de la caja',
    'cashRegister' => 'arqueo',
    'newBase' => 'nueva base'
  ];

  //--------------------------------------
  // PROPIEDADES COMPUTADAS
  //--------------------------------------

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

  //----------------------------------------------------------------
  // RENDERIZACIÓN
  //----------------------------------------------------------------
  public function render()
  {
    $data = $this->init();
    return view('livewire.cash-control.show-boxs', compact('data'))->layout('livewire.cash-control.show-box.index');
  }

  /**
   * Provee los datos necesarios para que el componente empiece a
   * proces los datos que se desean mostrar.
   */
  public function init()
  {
    return [
      'business' => $this->getBusiness(),
      'boxs' => $this->getBoxs(),
      'transactionTypes' => $this->getTransactionTypesProperty()
    ];
  }

  //-----------------------------------------------------------------
  // UTILIDADES DEL COMPONENTE
  //-----------------------------------------------------------------
  /**
   * Se encarga de crear las instncias de las cajas, que son 
   * consumidas por el cliente.
   */
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

  public function storeClosingBox($data)
  {
    $ok = false;            //Determina si el proceso fue correctamente
    $errors = null;         //Guarda los errores de la validación
    $log = [];

    $rules = $this->closingBoxRules();
    $attributes = $this->closingBoxAttributes;

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

  public function destroyTransaction($id)
  {
    //TODO
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
}
