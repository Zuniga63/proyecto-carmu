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
  public $moment = 'now';
  public $date = '';
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
      $rules = array_merge($rules, [
        'date' => "required|date|before_or_equal:$this->maxDate"
      ]);
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

  public function getPeriodDatesProperty()
  {
    $now = Carbon::now();
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

    $data = DB::connection('carmu')
      ->table('sale')
      ->where('sale_date', '>=', $min->format($format))
      ->where('sale_date', '<=', $max->format($format))
      ->orderBy('sale_id')
      ->orderBy('sale_date')
      ->get();
    
    foreach($data as $sale){
      $result [] = [
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

    foreach($this->sales as $sale){
      $amount = $sale['amount'];
      if($first){
        $minSale = $amount;
        $maxSale = $amount;
        $first = false;
      }else{
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
    $saleData = [
      'description' => $this->description,
      'amount' => $this->amount
    ];

    $saleData = $this->moment === 'other'
      ? array_merge($saleData, ['sale_date' => $this->date])
      : $saleData;

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
      $this->reset('moment', 'description', 'amount', 'categoryId');
      $this->emit('stored');
    } catch (\Throwable $th) {
      DB::rollBack();  
      $this->emit('storedError');
    }
  }

  public function resetFields()
  {
    $this->reset('moment', 'description', 'amount', 'categoryId');
    $this->emit('reset');
  }
}
