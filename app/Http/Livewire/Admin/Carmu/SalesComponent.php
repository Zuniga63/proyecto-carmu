<?php

namespace App\Http\Livewire\Admin\Carmu;

use App\Models\CashControl\Box;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SalesComponent extends Component
{
  public $view = 'create';    //Para gestionar el formulario
  protected $timezome = 'America/Bogota';

  //--------------------------------------------------------
  //  PROPIEDADES DEL FORMULARIO
  //--------------------------------------------------------
  public $saleId = null;        //Para cuando se va a actualizar una venta
  public $moment = 'now';       //El momento en el que se realiza la venta
  public $saleType = 'cash';    //Para actualizar el valor de la caja
  public $date = '';            //La fecha en formato Y-m-d
  public $setTime = false;      //Si se va a especificar la hora
  public $time = "";            //La hora en formato H:i
  public $categoryId = ' ';     //El identificador de la categoría
  public $description = '';     //Los detalles de la venta
  public $amount = '';          //El importe de la venta


  //--------------------------------------------------------
  //  PROPIEDADES COMPUTADAS RELACIONADAS AL FORMULARIO
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
    return Carbon::now()->timezone($this->timezome)->format('Y-m-d');
  }

  public function getNowProperty()
  {
    return Carbon::now()->isoFormat('MMMM Do YYYY hh:mm:ss a');
  }

  //--------------------------------------------------------
  //  REGLAS DE VALIDACION
  //--------------------------------------------------------
  /**
   * Contiene los nombre de los atributos
   * en español para la vista
   */
  protected $attributes = [
    'moment' => 'Momento de la venta',
    'saleType' => 'Forma de pago',
    'date' => 'Fecha',
    'time' => 'Hora',
    'categoryId' => 'Categoría',
    'description' => 'Descripción',
    'amount' => 'Importe'
  ];

  /**
   * Especifica las reglas de vaidación segun el estado de los campos 
   * a insertar en la base de datos
   */
  protected function rules()
  {
    $rules = [
      'moment' => ['required', 'string', Rule::in(['now', 'other'])],
      'saleType' => ['required', 'string', Rule::in(['cash', 'card'])],
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
      } //end if-else
    } //end if

    return $rules;
  }

  //--------------------------------------------------------
  //  SISTEMA DE CONSULTA
  //--------------------------------------------------------
  /**
   * Es el listado periodo en el cual se van a mostrar los registros
   */
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

  /**
   * Es el periodo elegido por el usuario para filtrar los
   * datos de las ventas
   */
  public $period = 'today';

  /**
   * es el identificador de la categoría por medio del caul se van a 
   * filtrar los datos de las ventas, por defecto es all.
   */
  public $periodCategory = 'all';

  /**
   * Es el listado de filtro de datos para poder comparar 
   * los datos de ventas
   */
  public $graphPeriods = [
    'weekly' => 'Semanal',
    'weeklyAccumulated' => 'Semanal acumulado',
    'monthly' => 'Mensual'
  ];

  /**
   * Corresponde al filtro seleccionado por el cliente para
   * poder obtener los datos de las graficas a mostrar
   */
  public $graphPeriod = 'weekly';

  /**
   * Al igual que los datos de las ventas es utilizado 
   * para poder filtar las ventas
   */
  public $graphCategory = 'all';

  /**
   * Define la fena de inicio y final del peridodo
   * que es util para las consultas
   */
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

  /**
   * Retorna un arreglo con los datos segun los campos $period and $periodCategory
   */
  public function getSalesProperty()
  {
    $min = $this->periodDates['min'];
    $max = $this->periodDates['max'];
    $format = 'Y-m-d H:i:s';
    $result = [];
    $data = [];

    if ($this->periodCategory === 'all') {
      $data = DB::connection('carmu')
        ->table('sale')
        ->where('sale_date', '>=', $min->format($format))
        ->where('sale_date', '<=', $max->format($format))
        ->orderBy('sale_id')
        ->orderBy('sale_date')
        ->get();
    } else {
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

  /**
   * Recopila estadisticas de ventas, por el momento solo recupera el saldo total
   * y la venta minima y maxima
   */
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

  /**
   * Este metodo retorna los datos listos para poder
   * ser consumidos por la grafica del componente.
   */
  public function graphData()
  {
    $dayOfWeek = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
    $colors = [
      'rgba(201, 203, 207, 0.8)', //gray
      'rgba(255, 99, 132, 0.8)',  //Red
      'rgba(255, 159, 64, 0.8)',  //Orange
      'rgba(75, 192, 192, 0.8)',  //green
      'rgba(54, 162, 235, 0.8)',  //blue
      'rgba(153, 102, 255, 0.8)', //purple
      'rgba(255, 205, 86, 0.8)',  //yellow
    ];
    $borderColors = [
      'rgba(201, 203, 207, 1)', //gray
      'rgba(255, 99, 132, 1)',  //Red
      'rgba(255, 159, 64, 1)',  //Orange
      'rgba(75, 192, 192, 1)',  //green
      'rgba(54, 162, 235, 1)',  //blue
      'rgba(153, 102, 255, 1)', //purple
      'rgba(255, 205, 86, 1)',  //yellow
    ];
    $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $now = Carbon::now()->timezone($this->timezome);
    $data = [];

    switch ($this->graphPeriod) {
      case 'weekly':
        $data = $this->getDataFromWeeklySales($colors, $borderColors);
        break;
      case 'weeklyAccumulated':
        $data = $this->getDataFromWeeklySales($colors, $borderColors, true);
        break;
      case 'monthly':
        $labels = [];
        $date = $now->copy()->subMonths(5)->startOfMonth()->startOfDay();
        $end = $now->copy()->endOfDay();
        $count = 0;

        for ($i = 1; $i < 32; $i++) {
          $labels[] = $i;
        }

        while ($date->lessThanOrEqualTo($end)) {
          $label = $months[$date->month - 1];
          $data = [];
          $endOfMonth = $date->copy()->endOfMonth()->endOfDay();
          $sale = 0;

          while ($date->lessThanOrEqualTo($endOfMonth) && $date->lessThanOrEqualTo($end)) {
            $sale += $this->getSumFromGraphPeriod($date, $this->graphCategory);
            $data[] = $sale;
            $date->addDay();
          }

          $datasets[] = [
            'label' => $label,
            'backgroundColor' => $colors[$count],
            'borderColor' => $borderColors[$count],
            'borderWidth' => 1,
            'data' => $data,
            'fill' => false
          ];

          $count++;
        }

        $data = [
          'labels' => $labels,
          'datasets' => $datasets,
          'type' => 'line'
        ];
        break;
      default:
        # code...
        break;
    }
    return $data;
  }

  /**
   * Este metodo se encarga de consultar el total de las
   * ventas para la fecha y retorna su valor. Solo para los
   * @param Carbon $date Fecha en la que se va a sumar los saldos
   * @param string $category  ID de la categoría o all
   * @return float Suma de los importes de las ventas
   */
  protected function getSumFromGraphPeriod($date, $category)
  {
    $startDay = $date->copy()->startOfDay();
    $endDay = $startDay->copy()->endOfDay();
    $consultFormat = 'Y-m-d H:i:s';
    $result = 0;

    try {
      if ($this->graphCategory === 'all') {
        $sum = DB::connection('carmu')
          ->table('sale')
          ->where('sale_date', '>=', $startDay->format($consultFormat))
          ->where('sale_date', '<=', $endDay->format($consultFormat))
          ->sum('amount');
        $result += floatval($sum);
      } else {
        $sum = DB::connection('carmu')
          ->table('sale as t1')
          ->join('sale_has_category as t2', 't1.sale_id', '=', 't2.sale_id')
          ->where('t2.category_id', $category)
          ->where('t1.sale_date', '>=', $startDay->format($consultFormat))
          ->where('t1.sale_date', '<=', $endDay->format($consultFormat))
          ->sum('t1.amount');
        $result += floatval($sum);
      }
      //code...
    } catch (\Throwable $th) {
      return 0;
    }

    return $result;
  }

  /**
   * Este metodo retorna los datos para las graficas semanales que pueden ser
   * datos individuales o semanales
   * @param array $backgrounds Arreglo con los formatos de color de fondo rgba
   * @param array $borders Arreglo con los formatos de color para los border rgba
   * @param bool $accumulated Define si los saldos son individuales o acumulados
   * @return array Arreglo con los datos para la grafíca
   */
  protected function getDataFromWeeklySales($backgrounds, $borders, $accumulated = false)
  {
    $now = Carbon::now()->timezone($this->timezome);
    $labels = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
    $date = $now->copy()->subWeeks(3)->startOfWeek()->startOfDay();
    $end = $now->copy()->endOfWeek()->endOfDay();
    $datasets = [];
    $week = 0;
    $weekNames = ['Hace 3 sem', 'Hace 2 sem', 'Hace 1 sem', 'Actual'];

    while ($date->lessThanOrEqualTo($end)) {
      $label = $weekNames[$week] . " ($date->isoWeek)";
      $data = [];
      $endOfWeek = $date->copy()->endOfWeek();
      $amountAccumulated = 0;

      while ($date->lessThanOrEqualTo($endOfWeek)) {
        $amount = $this->getSumFromGraphPeriod($date, $this->graphCategory);
        $amountAccumulated += $amount;
        if($accumulated){
          $data[] = $amountAccumulated;
        }else{
          $data[] = $amount;
        }

        $date->addDay();
      }

      $dataset = [
        'label' => $label,
        'backgroundColor' => $backgrounds[$week],
        'borderColor' => $borders[$week],
        'borderWidth' => 1,
        'data' => $data,
      ];

      if($accumulated){
        $dataset = array_merge($dataset, ['fill' => false]);
      }
      $datasets[] = $dataset;


      $week++;
    } //end while

    return [
      'labels' => $labels,
      'datasets' => $datasets,
      'type' => $accumulated ? 'line' : 'bar'
    ];
  }


  public function render()
  {
    // dd($this->graphData);
    return view('livewire.admin.carmu.sales-component')
      ->layout('admin.carmu.sales.index');
  }

  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $saleData = $this->buildData();

    DB::connection('carmu')->beginTransaction();
    DB::beginTransaction();

    //Se establece la zona horaria 
    DB::connection('carmu')->statement('SET time_zone = "-05:00";');
    DB::statement('SET time_zone = "-05:00";');
    try {
      /** @var Box */
      $localBox = Box::where('business_id', 1)->where('main', 1)->first();
      /** @var Box */
      $majorBox = Box::where('business_id', 1)->where('main', 0)->first();
      $date = null;

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

      //Se actualiza la fecha
      if($this->moment !== 'now'){
        if($this->setTime){
          $date = $this->date . ' ' . $this->time;
        }else{
          $date = Carbon::createFromFormat('Y-m-d', $this->date)->endOfDay()->format('Y-m-d H:i:s');
        }
      }
      //Ahora se actualiza la caja
      if($this->saleType === 'cash' && $localBox){
        if($date){
          $localBox->transactions()->create([
            'description' => $this->description . " [Efectivo]",
            'amount'      => $this->amount,
            'type'        => 'sale',
            'transaction_date' => $date
          ]);
        }else{
          $localBox->transactions()->create([
            'description' => $this->description . " [Efectivo]",
            'amount'      => $this->amount,
            'type'        => 'sale',
          ]);
        }  
      }else{
        if($date){
          $majorBox->transactions()->create([
            'description' => $this->description . " [Tarjeta]",
            'amount'      => $this->amount,
            'type'        => 'sale',
            'transaction_date' => $date
          ]);
        }else{
          $majorBox->transactions()->create([
            'description' => $this->description . " [Tarjeta]",
            'amount'      => $this->amount,
            'type'        => 'sale',
          ]);
        }  
      }
      //Se emite el evento de guardado
      DB::connection('carmu')->commit();
      DB::commit();
      $this->resetFields();
      $this->emit('stored');
    } catch (\Throwable $th) {
      DB::rollBack();
      $this->emit('serverError');
    }
  }

  /**
   * Se encarga de montar los datos de la venta que
   * se quiere actualizar
   */
  public function edit($id)
  {
    try {
      $sale = DB::connection('carmu')
        ->table('sale')
        ->where('sale_id', $id)
        ->first();

      if ($sale) {
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

        if ($relation) {
          $this->categoryId = $relation->category_id;
        }

        $this->view = 'edit';
        $this->emit('saleMount', $this->amount);
      } else {
        $this->emit('saleNotFound');
      }
    } catch (\Throwable $th) {
      $this->emit('serverError');
    }
  }

  /**
   * Actualiza los datos de la venta en la base de datos
   */
  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $saleData = $this->buildData();

    DB::beginTransaction();
    DB::statement('SET time_zone = "-05:00";');
    try {
      if (DB::connection('carmu')->table('sale')->where('sale_id', $this->saleId)->exists()) {
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
      } else {
        $this->emit('saleNotFound');
        DB::rollBack();
      }
    } catch (\Throwable $th) {
      dd($th);
      $this->emit('serverError');
      DB::rollBack();
    }
  }

  /**
   * Se encarga de construir los datos que se pasarán al
   * querybuilder para insertar o actualizar datos.
   */
  protected function buildData()
  {
    $saleData = [
      'description' => $this->description,
      'amount' => $this->amount
    ];

    if ($this->moment === 'other') {
      if ($this->setTime) {
        $date = Carbon::createFromFormat('Y-m-d H:i', "$this->date $this->time");
        $saleData = array_merge($saleData, ['sale_date' => $date]);
      } else {
        $saleData = array_merge($saleData, ['sale_date' => $this->date]);
      }
    } else if ($this->view === 'edit') {
      $saleData = array_merge($saleData, ['sale_date' => Carbon::now()->timezone('America/Bogota')]);
    }

    return $saleData;
  }

  public function resetFields()
  {
    $this->reset('saleId', 'view', 'moment', 'saleType', 'description', 'amount', 'categoryId', 'setTime');
    $this->emit('reset');
  }
}
