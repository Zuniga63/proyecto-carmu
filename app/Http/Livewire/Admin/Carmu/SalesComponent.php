<?php

namespace App\Http\Livewire\Admin\Carmu;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SalesComponent extends Component
{
  public $view = 'create';    //Para gestionar el formulario

  //--------------------------------------------------------
  //  PROPIEDADES DEL FORMULARIO
  //--------------------------------------------------------
  public $saleId = null;
  public $moment = 'now';
  public $date = '';
  public $setTime = false;
  public $time = "";
  public $categoryId = ' ';
  public $description = '';
  public $amount = '';


  //--------------------------------------------------------
  //  PROPIEDADES COMPUTADAS
  //--------------------------------------------------------

  /**
   * Recupera de la base de datos los nombres de las categorías
   * junto con sus IDs
   */
  public function getCategoriesProperty()
  {
    return DB::connection('carmu')
      ->table('sale_category')
      ->orderBy('name')
      ->pluck('name', 'category_id')
      ->toArray();
  }

  /**
   * Retorna la fecha actual para el campo del formulario
   */
  public function getMaxDateProperty()
  {
    return Carbon::now()->format('Y-m-d');
  }

  //--------------------------------------------------------
  //  REGLAS DE VALIDACION
  //--------------------------------------------------------
  protected $attributes = [
    'moment' => 'Momento de la venta',
    'date' => 'Fecha',
    'time' => 'Hora',
    'categoryId' => 'Categoría',
    'description' => 'Descripción',
    'amount' => 'Importe'
  ];

  protected function rules()
  {
    $rules = [
      'moment' => ['required', 'string', Rule::in(['now', 'other'])],
      'description' => 'required|max:255',
      'amount' => 'required|numeric|min:1000',
      'categoryId' => 'required|numeric|min:1|exists:carmu.sale_category,category_id'
    ];

    if ($this->moment === 'other') {

      if ($this->setTime) {
        $rules = array_merge($rules, [
          'date' => "required|date|before_or_equal:$this->maxDate",
          'time' => "required|date_format:H:i"
        ]);
      } else {
        $rules = array_merge($rules, [
          'date' => "required|date|before_or_equal:$this->maxDate"
        ]);
      }
    }

    return $rules;
  }

  //--------------------------------------------------------
  //  SISTEMA DE CONSULTA
  //--------------------------------------------------------
  public $periods = [
    'today' => 'El día de hoy',
    'yesterday' => 'El día de ayer',
    'thisWeek' => 'Esta semana',
    'lastWeek' => 'La semana pasada',
    'thisFortnight' => 'Esta quincena',
    'lastFortnight' => 'La quincena pasada',
    'thisMonth' => 'Este mes',
    'lastMonth' => 'El mes pasado',
    // 'other' => 'Otras fechas',
  ];

  public $period = 'today';

  public $periodCategory = 'all';

  public function getPeriodDatesProperty()
  {
    $now = Carbon::now()->timezone("America/Bogota");
    $min = $now->copy();
    $max = $now->copy();

    switch ($this->period) {
      case 'today':
        break;
      case 'yesterday':
        $min->subDay();
        $max->subDay();
        break;
      case 'thisWeek':
        $min->startOfWeek();
        $max->endOfWeek();
        break;
      case 'lastWeek':
        $min->subWeek()->startOfWeek();
        $max->subWeek()->endOfWeek();
        break;
      case 'thisFortnight':
        if ($min->day > 15) {
          $min->day(16);
          $max->endOfMonth();
        } else {
          $min->startOfMonth();
          $max->startOfMonth()->addDays(14);
        }
        break;
      case 'lastFortnight':
        if ($min->day > 15) {
          $min->startOfMonth();
          $max->startOfMonth()->addDays(14);
        } else {
          $min->subMonth()->day(16);
          $max->subMonth()->endOfMonth();
        }
        break;
      case 'thisMonth':
        $min->startOfMonth();
        $max->endOfMonth();
        break;
      case 'lastMonth':
        $min->subMonth()->startOfMonth();
        $max->subMonth()->endOfMonth();
        break;

      default:
        # code...
        break;
    }

    $min->startOfDay();
    $max->endOfDay();
    $format = 'd-m-y H:i:s';

    return [
      'min' => $min,
      'minView' => $min->format($format),
      'max' => $max,
      'maxView' => $max->format($format),
    ];
  }

  public function getSalesProperty()
  {
    $min = $this->periodDates['min'];
    $max = $this->periodDates['max'];
    $format = 'Y-m-d H:i:s';
    $result = [];
    $data = [];

    if($this->periodCategory === 'all'){
      $data = DB::connection('carmu')
        ->table('sale')
        ->where('sale_date', '>=', $min->format($format))
        ->where('sale_date', '<=', $max->format($format))
        ->orderBy('sale_id')
        ->orderBy('sale_date')
        ->get();
    }else{
      $data = DB::connection('carmu')
        ->table('sale as t1')
        ->join('sale_has_category as t2', 't1.sale_id', '=', 't2.sale_id')
        ->where('t2.category_id', $this->periodCategory)
        ->where('sale_date', '>=', $min->format($format))
        ->where('sale_date', '<=', $max->format($format))
        ->select('t1.*')
        ->get();
    }

    foreach ($data as $sale) {
      $result[] = [
        'id' => $sale->sale_id,
        'date' => Carbon::createFromFormat('Y-m-d H:i:s', $sale->sale_date)->format('d-m-Y'),
        'description' => $sale->description,
        'amount' => floatval($sale->amount)
      ];
    }

    return $result;
  }

  public function getSaleStatisticsProperty()
  {
    $minSale = 0;
    $maxSale = 0;
    $total = 0;
    $first = true;

    foreach ($this->sales as $sale) {
      $amount = $sale['amount'];
      if ($first) {
        $minSale = $amount;
        $maxSale = $amount;
        $first = false;
      } else {
        $minSale = $minSale <= $amount ? $minSale : $amount;
        $maxSale = $maxSale >= $amount ? $maxSale : $amount;
      }
      $total += $amount;
    }

    return [
      'min' => $minSale,
      'max' => $maxSale,
      'total' => $total
    ];
  }


  public function render()
  {
    return view('livewire.admin.carmu.sales-component')
      ->layout('admin.carmu.sales.index');
  }

  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $saleData = $this->buildData();

    DB::beginTransaction();
    try {
      //En primer lugar se crea el registro de venta
      $id = DB::connection('carmu')
        ->table('sale')
        ->insertGetId($saleData);
      //Ahora se agrega la relacion con la categoría
      DB::connection('carmu')
        ->table('sale_has_category')
        ->insert([
          'sale_id' => $id,
          'category_id' => $this->categoryId
        ]);
      //Se emite el evento de guardado
      DB::commit();
      $this->resetFields();
      $this->emit('stored');
    } catch (\Throwable $th) {
      DB::rollBack();
      $this->emit('serverError');
    }
  }

  public function edit($id)
  {
    try {
      $sale = DB::connection('carmu')
        ->table('sale')
        ->where('sale_id', $id)
        ->first();
      
      if($sale){
        $this->saleId = $sale->sale_id;
        $this->moment = 'other';
        $this->setTime = true;
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $sale->sale_date);
        $this->date = $date->format('Y-m-d');
        $this->time = $date->format('H:i');
        $this->description = $sale->description;
        $this->amount = $sale->amount;

        //Ahora recupero el id de la categoría
        $relation = DB::connection('carmu')
          ->table('sale_has_category')
          ->where('sale_id', $this->saleId)
          ->first();
        
        if($relation){
          $this->categoryId = $relation->category_id;
        }

        $this->view = 'edit';
        $this->emit('saleMount', $this->amount);
      }else{
        $this->emit('saleNotFound');
      }
    } catch (\Throwable $th) {
      $this->emit('serverError');
    }
  }

  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $saleData = $this->buildData();

    DB::beginTransaction();
    try {
      if(DB::connection('carmu')->table('sale')->where('sale_id', $this->saleId)->exists()){
        //En primer lugar lo que hago es modificar los datos de la venta
        DB::connection('carmu')->table('sale')
          ->where('sale_id', $this->saleId)
          ->update($saleData);

        /**
         * Se descruyen las relaciones de la venta
         */
        DB::connection('carmu')->table('sale_has_category')
          ->where('sale_id', $this->saleId)
          ->delete();
        
        //Se crean nuvamente las relaciones
        DB::connection('carmu')->table('sale_has_category')
          ->insert([
            'sale_id' => $this->saleId,
            'category_id' => $this->categoryId
          ]);

        $this->emit('updated');
        $this->resetFields();
        DB::commit();
      }else{
        $this->emit('saleNotFound');
        DB::rollBack();
      }
    } catch (\Throwable $th) {
      dd($th);
      $this->emit('serverError');
      DB::rollBack();
    }
  }

  protected function buildData()
  {
    $saleData = [
      'description' => $this->description,
      'amount' => $this->amount
    ];

    if($this->moment === 'other'){
      if($this->setTime){
        $date = Carbon::createFromFormat('Y-m-d H:i', "$this->date $this->time");
        $saleData = array_merge($saleData, ['sale_date' => $date]);
      }else{
        $saleData = array_merge($saleData, ['sale_date' => $this->date]);
      }
    }else if($this->view === 'edit'){
      $saleData = array_merge($saleData, ['sale_date' => Carbon::now()->timezone('America/Bogota')]);
    }

    return $saleData;
  }

  public function resetFields()
  {
    $this->reset('saleId', 'view', 'moment', 'description', 'amount', 'categoryId', 'setTime');
    $this->emit('reset');
  }
}
