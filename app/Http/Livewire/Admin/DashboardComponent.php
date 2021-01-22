<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use PhpParser\Node\Stmt\Break_;

use function PHPUnit\Framework\returnSelf;

class DashboardComponent extends Component
{
  public Carbon $now;
  public $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

  public function mount()
  {
    $now = Carbon::now()->timezone("America/Bogota");
    $this->now = $now;
  }

  public function render()
  {
    $data = $this->getData();
    $months = $this->months;
    return view('livewire.admin.dashboard-component', compact('months', 'data'))
      ->layout("admin.dashboard.index");
  }

  /**
   * Retorna un arreglo con todos los datos requeridos
   * por el componente para que pueda ser consumido por el
   * frontend
   */
  protected function getData()
  {
    $start = Carbon::now();
    $now = $this->now->copy();
    $year = $now->year;
    $month = $now->month;
    $tremester = ceil($month / 3.0);
    $semester = ceil($month / 6.0);

    $data = [
      'month' => $month,
      'tremester' => $tremester,
      'semester' => $semester,
      'year' => $year,
      'months' => $this->months,
      'url' => route('admin.carmu_profile'),
      'sales' => $this->getAnnualRecords('sales'),
      'payments' => $this->getAnnualRecords('payments'),
      'credits' => $this->getAnnualRecords('credits'),
      'salesByCategories' => $this->getMontlyReportsByCategories(),
      'debtEvolution' => $this->getDebtEvolution(),
      'history' => $this->getCustomersHistory(),
    ];

    $end = Carbon::now();
    $data['time'] = $end->diffInMilliseconds($start);

    return $data;
  }

  /**
   * METODO DEPRECADO
   */
  protected function getMontlyReports()
  {
    $result = [];
    $reports = [];
    $start = Carbon::now()->startOfYear();
    $totalSales = 0;
    $totalCredits = 0;
    $totalPayments = 0;
    $totalBalance = 0;

    for ($index = 0; $index < 12; $index++) {
      $sales = DB::connection('carmu')
        ->table('sale')
        ->where('sale_date', '>=', $start->format('Y-m-d H:i:s'))
        ->where('sale_date', '<=', $start->copy()->endOfMonth()->format('Y-m-d H:i:s'))
        ->sum('amount');

      $credits = DB::connection('carmu')
        ->table('customer_credit')
        ->where('credit_date', '>=', $start->format('Y-m-d H:i:s'))
        ->where('credit_date', '<=', $start->copy()->endOfMonth()->format('Y-m-d H:i:s'))
        ->sum('amount');

      $payments = DB::connection('carmu')
        ->table('customer_payment')
        ->where('payment_date', '>=', $start->format('Y-m-d H:i:s'))
        ->where('payment_date', '<=', $start->copy()->endOfMonth()->format('Y-m-d H:i:s'))
        ->sum('amount');

      $balance = $sales + $payments - $credits;
      $totalSales += $sales;
      $totalPayments += $payments;
      $totalCredits += $credits;
      $totalBalance += $balance;
      // dd($payments + $sales - $credits);
      $reports[] = ['month' => $this->months[$index], 'sales' => $sales, 'credits' => $credits, 'payments' => $payments, 'balance' => $balance];
      $start->addMonth();
    }

    return [
      'reports' => $reports,
      'totalSales' => $totalSales,
      'totalPayments' => $totalPayments,
      'totalCredits' => $totalCredits,
      'totalBalance' => $totalBalance
    ];
  }

  public function getCategories()
  {
    return DB::connection('carmu')
      ->table('sale_category')
      ->pluck('name', 'category_id');
  }

  protected function getMontlyReportsByCategories()
  {
    $categories = $this->getCategories();
    $initialReports = [];
    foreach ($categories as $id => $categoryName) {
      $montlyReports = $this->getMontlyReportsByCategory($id);
      $initialReports[] = [
        'id' => $id,
        'name' => $categoryName,
        'sales' => $montlyReports['sales'],
        'amount' => $montlyReports['amount'],
        'average' => $montlyReports['amount'] / 12
      ];
    }

    usort($initialReports, function ($a, $b) {
      if ($a['amount'] == $b['amount']) {
        return 0;
      }
      return ($a['amount'] < $b['amount']) ? '+1' : '-1';
    });

    return $initialReports;
  }

  protected function sortByAmount($a, $b)
  {
    return $a['amount'] - $b['amount'];
  }

  protected function getMontlyReportsByCategory($id = 4)
  {
    $sales = [];
    $amount = 0;

    $from = Carbon::now()->startOfYear();

    for ($index = 0; $index < 12; $index++) {
      $sale = floatval(DB::connection('carmu')
        ->table('sale_has_category')
        ->where('category_id', $id)
        ->join('sale', 'sale.sale_id', '=', 'sale_has_category.sale_id')
        ->where('sale.sale_date', '>=', $from->format('Y-m-d H:i:s'))
        ->where('sale.sale_date', '<=', $from->copy()->endOfMonth()->format('Y-m-d H:i:s'))
        ->select('sale.amount')
        ->sum('amount'));

      $sales[] = $sale;
      $amount += $sale;

      $from->addMonth();
    }

    return [
      'sales' => $sales,
      'amount' => $amount
    ];
  }

  protected function creditEvolution()
  {

    $montlyReports = [];

    $from = Carbon::now()->startOfYear();
    //Calculo el saldo de los creditos del año anterior
    $initialCredits = floatval(
      DB::connection('carmu')
        ->table('customer_credit')
        ->where('credit_date', '<', $from->format('Y-m-d H:i:s'))
        ->sum('amount')
    );

    $initialPayments = floatval(
      DB::connection('carmu')
        ->table('customer_payment')
        ->where('payment_date', '<', $from->format('Y-m-d H:i:s'))
        ->sum('amount')
    );

    $initialArchivedCredits = floatval(
      DB::connection('carmu')
        ->table('customer as t1')
        ->where('t1.archived', 1)
        ->join('customer_credit as t2', 't1.customer_id', '=', 't2.customer_id')
        ->select('t2.*')
        ->where('credit_date', '<', $from->format('Y-m-d H:i:s'))
        ->sum('amount')
    );

    $initialArchivedPayments = floatval(
      DB::connection('carmu')
        ->table('customer as t1')
        ->where('t1.archived', 1)
        ->join('customer_payment as t2', 't1.customer_id', '=', 't2.customer_id')
        ->select('t2.*')
        ->where('payment_date', '<', $from->format('Y-m-d H:i:s'))
        ->sum('amount')
    );

    // dd($initialArchivedCredits - $initialArchivedPayments);

    $initialBalance = $initialCredits - $initialPayments - ($initialArchivedCredits - $initialArchivedPayments);
    $balance = $initialBalance;

    for ($index = 0; $index < 12; $index++) {
      $credits = floatval(DB::connection('carmu')
        ->table('customer_credit')
        ->where('credit_date', '>=', $from->format('Y-m-d H:i:s'))
        ->where('credit_date', '<=', $from->copy()->endOfMonth()->format('Y-m-d H:i:s'))
        ->sum('amount'));

      $payments = floatval(DB::connection('carmu')
        ->table('customer_payment')
        ->where('payment_date', '>=', $from->format('Y-m-d H:i:s'))
        ->where('payment_date', '<=', $from->copy()->endOfMonth()->format('Y-m-d H:i:s'))
        ->sum('amount'));

      $grow = ($credits - $payments) / $balance;

      $balance += $credits - $payments;

      $montlyReports[] = [
        'month' => $this->months[$index],
        'credits' => $credits,
        'payments' => $payments,
        'balance' => $balance,
        'grow' => $grow
      ];

      $from->addMonth();
    }

    return [
      'inititalBalance' => $initialBalance,
      'reports' => $montlyReports
    ];
  }

  /**
   * Este metodo se encarga de recuperar los registros diarios,
   * mensuales y anaules de los ultimos dos años almacenados en la base de datos
   * @param string $type Tipo de movimiento, credito, abono o venta
   */
  protected function getAnnualRecords(string $type)
  {
    $thisYear = $this->now->copy()->startOfYear();
    $lastYear = $thisYear->copy()->subYear()->startOfYear();

    switch ($type) {
      case 'sales':
        return [
          $this->getSaleOfYear($lastYear),
          $this->getSaleOfYear($thisYear)
        ];
        break;
      case 'payments':
        return [
          $this->getCustomerPaymentsOfYear($lastYear),
          $this->getCustomerPaymentsOfYear($thisYear)
        ];
        break;
      case 'credits':
        return [
          $this->getCustomerCreditsOfYear($lastYear),
          $this->getCustomerCreditsOfYear($thisYear)
        ];
        break;
      default:
        return [];
        break;
    }
  }
  /**
   * Este metodo se encarga de recuperar las ventas 
   * de los ultimos dos años, clasificandolos por meses y
   * luego por días
   */


  protected function getSaleOfYear(Carbon $startDate)
  {
    $formatDate = 'Y-m-d H:i:s';
    $year = $startDate->year;
    $annualSale = 0;
    $monthlySales = [];
    for ($index = 0; $index < 12; $index++) {
      $month = $startDate->month;
      $monthName = $this->months[$index];
      $monthlySale = 0;
      $dailySales = [];

      /**
       * Se establecen las fechas limites para las consultas
       * de las ventas diarias
       */
      $startOfMonth = $startDate->copy();
      $endOfMonth = $startOfMonth->copy()->endOfMonth();

      while ($startDate->greaterThanOrEqualTo($startOfMonth) && $startDate->lessThanOrEqualTo($endOfMonth)) {
        $startDay = $startDate->copy()->startOfDay();
        $endDay = $startDay->copy()->endOfDay();

        $sale = floatval(DB::connection('carmu')
          ->table('sale')
          ->where('sale_date', '>=', $startDay->format($formatDate))
          ->where('sale_date', '<=', $endDay->format($formatDate))
          ->sum('amount'));

        $monthlySale += $sale;
        $dailySales[] = [
          'partial' => $sale,
          'accumulated' => $monthlySale
        ];
        $annualSale += $sale;
        $startDate->addDay();
      }

      if ($year === 2020 && 1 <= $month && $month <= 6) {
        $temporalAccumulated = 0;
        $averageSale = $monthlySale / count($dailySales);
        foreach ($dailySales as $key => $sale) {
          $temporalAccumulated += $averageSale;
          $dailySales[$key]['partial'] = $averageSale;
          $dailySales[$key]['accumulated'] = $temporalAccumulated;
        }
      }

      $monthlySales[] = [
        'month' => $month,
        'name' => $monthName,
        'partial' => $monthlySale,
        'accumulated' => $annualSale,
        'dailySales' => $dailySales,
      ];
    }

    return [
      'year' => $year,
      'annualSale' => $annualSale,
      'monthlySales' => $monthlySales
    ];
  }

  protected function getCustomerPaymentsOfYear(Carbon $startDate)
  {
    $formatDate = 'Y-m-d H:i:s';
    $year = $startDate->year;
    $annualPayment = 0;
    $monthlyPayments = [];

    for ($index = 0; $index < 12; $index++) {
      $month = $startDate->month;
      $monthName = $this->months[$index];
      $monthPayment = 0;
      $dailyPayments = [];

      /**
       * Ahora se definen las fecha limitantes 
       * de las consultas a la base de datos
       */
      $startOfMonth = $startDate->copy();
      $endOfMonth = $startOfMonth->copy()->endOfMonth();

      /**
       * Se recorre en bucle para recuperar los abonos diarios
       */
      while ($startDate->greaterThanOrEqualTo($startOfMonth) && $startDate->lessThanOrEqualTo($endOfMonth)) {
        $startDay = $startDate->copy()->startOfDay();
        $endDay = $startDay->copy()->endOfDay();

        /**
         * Ahora se realiza la consulta a la base de datos
         */
        $payment = DB::connection('carmu')
          ->table('customer_payment')
          ->where('payment_date', '>=', $startDay->format($formatDate))
          ->where('payment_date', '<=', $endDay->format($formatDate))
          ->sum('amount');
        $payment = floatval($payment);

        /**
         * Ahora se actualizan las variables
         */
        $monthPayment += $payment;
        $dailyPayments[] = [
          'partial' => $payment,
          'accumulated' => $monthPayment
        ];
        $annualPayment += $payment;

        /**
         * Finalmente aumento en un día la fecha
         */
        $startDate->addDay();
      } //end while

      /**
       * Ahora se actualizan las variables del mes
       */
      $monthlyPayments[] = [
        'month' => $month,
        'name' => $monthName,
        'partial' => $monthPayment,
        'accumulated' => $annualPayment,
        'dailyPayments' => $dailyPayments
      ];
    } //end for

    return [
      'year' => $year,
      'annualPayment' => $annualPayment,
      'monthlyPayments' => $monthlyPayments
    ];
  } //end method

  protected function getCustomerCreditsOfYear(Carbon $startDate)
  {
    $formatDate = 'Y-m-d H:i:s';
    $year = $startDate->year;
    $annualCredit = 0;
    $monthlyCredits = [];

    for ($index = 0; $index < 12; $index++) {
      $month = $startDate->month;
      $monthName = $this->months[$index];
      $monthCredit = 0;
      $dailyCredits = [];

      /**
       * Ahora se definen las fecha limitantes 
       * de las consultas a la base de datos
       */
      $startOfMonth = $startDate->copy();
      $endOfMonth = $startOfMonth->copy()->endOfMonth();

      /**
       * Se recorre en bucle para recuperar los abonos diarios
       */
      while ($startDate->greaterThanOrEqualTo($startOfMonth) && $startDate->lessThanOrEqualTo($endOfMonth)) {
        $startDay = $startDate->copy()->startOfDay();
        $endDay = $startDay->copy()->endOfDay();

        /**
         * Ahora se realiza la consulta a la base de datos
         */
        $credit = DB::connection('carmu')
          ->table('customer_credit')
          ->where('credit_date', '>=', $startDay->format($formatDate))
          ->where('credit_date', '<=', $endDay->format($formatDate))
          ->sum('amount');
        $credit = floatval($credit);

        /**
         * Ahora se actualizan las variables
         */
        $monthCredit += $credit;
        $dailyCredits[] = [
          'partial' => $credit,
          'accumulated' => $monthCredit
        ];
        $annualCredit += $credit;

        /**
         * Finalmente aumento en un día la fecha
         */
        $startDate->addDay();
      } //end while

      /**
       * Ahora se actualizan las variables del mes
       */
      $monthlyCredits[] = [
        'month' => $month,
        'name' => $monthName,
        'partial' => $monthCredit,
        'accumulated' => $annualCredit,
        'dailyCredits' => $dailyCredits
      ];
    } //end for

    return [
      'year' => $year,
      'annualCredit' => $annualCredit,
      'monthlyCredits' => $monthlyCredits
    ];
  }

  /**
   * Retorna un arreglo con la evolucion de la deuda de
   * los clientes a lo largo de todo el año en curso
   */
  protected function getDebtEvolution()
  {
    $formatDate = 'Y-m-d H:i:s';
    $date = $this->now->copy()->startOfYear();

    /**
     * Se calcula el saldo efectivo de la deuda con el que se inicia 
     * el año en curso
     */
    $initialBalance = $this->getInitialCustomersDebts($date->format($formatDate));
    $initialArchivedBalance = $this->getInitialCustomersArchivedDebts($date->format($formatDate));
    $efectiveInitialBalance = $initialBalance - $initialArchivedBalance;
    $actualBalance = $efectiveInitialBalance;

    $monthlyEvolution = [];

    /**
     * Se calcula la evolucion de la deuda a lo largo
     * de todo el año
     */
    for ($index = 0; $index < 12; $index++) {
      $month = $date->month;
      $monthName = $this->months[$index];
      $baseDebt = $actualBalance;
      $monthDebt = 0;
      $monthlyGrow = 0;
      $dailyDebts = [];
      $startMonth = $date->copy()->startOfMonth();
      $endMonth = $startMonth->copy()->endOfMonth();

      while ($date->greaterThanOrEqualTo($startMonth) && $date->lessThanOrEqualTo($endMonth)) {
        $creditsAmount = 0;
        $paymentsAmount = 0;
        $dailyGrow = 0;
        $startDay = $date->copy()->startOfDay()->format($formatDate);
        $endDay = $date->copy()->endOfDay()->format($formatDate);

        if ($date->lessThanOrEqualTo($this->now)) {
          $creditsAmount = floatval(DB::connection('carmu')
            ->table('customer_credit')
            ->where('credit_date', '>=', $startDay)
            ->where('credit_date', '<=', $endDay)
            ->sum('amount'));

          $paymentsAmount = floatval(DB::connection('carmu')
            ->table('customer_payment')
            ->where('payment_date', '>=', $startDay)
            ->where('payment_date', '<=', $endDay)
            ->sum('amount'));
        }

        /**
         * Se hacen los calculos
         */
        $dailyDebt = $creditsAmount - $paymentsAmount;
        $dailyGrow = $actualBalance > 0 ? ($dailyDebt) / $actualBalance : 0;
        $monthDebt += $dailyDebt;
        $actualBalance += $dailyDebt;

        /**
         * Se crea el registro diario
         */
        $dailyDebts[] = [
          'partial' => $dailyDebt,
          'accumulated' => $actualBalance,
          'grow' => $dailyGrow,
        ];

        /**
         * Se incrementa la fecha 
         */
        $date->addDay();
      } //End while     

      /**
       * Se calcula la tasa de crecimiento del mes
       */
      $monthlyGrow = ($actualBalance - $baseDebt) / $actualBalance;

      $monthlyEvolution[] = [
        'month' => $month,
        'name' => $monthName,
        'initialDebt' => $baseDebt,
        'partial' => $monthDebt,
        'grow' => $monthlyGrow,
        'accumulated' => $actualBalance,
        'dailyEvolution' => $dailyDebts
      ];
    }

    return [
      'realBalance' => $initialBalance,
      'archivedBalance' => $initialArchivedBalance,
      'efectiveBalance' => $efectiveInitialBalance,
      'actualBalance' => $actualBalance,
      'monthlyEvolution' => $monthlyEvolution
    ];
  }

  /**
   * Calcula el valor de la deuda de los
   * clientes al iniciar el año
   * @param string $date Fecha del primero de enero del año en curso en formato Y-m-d H:i:s
   * @return float El Valor de la deuda de todos los clientes
   */
  protected function getInitialCustomersDebts(string $date)
  {
    $credits = floatval(
      DB::connection('carmu')
        ->table('customer_credit')
        ->where('credit_date', '<', $date)
        ->sum('amount')
    );

    $payments = floatval(
      DB::connection('carmu')
        ->table('customer_payment')
        ->where('payment_date', '<', $date)
        ->sum('amount')
    );

    return $credits - $payments;
  }

  /**
   * Calcual el saldo de los clientes que han sido archivados
   * @param string $date Fecha del primero de enero del año en curso en formato Y-m-d H:i:s
   * @return float El saldo de los clientes archivados
   */
  protected function getInitialCustomersArchivedDebts(string $date)
  {
    $credits = floatval(
      DB::connection('carmu')
        ->table('customer as t1')
        ->where('t1.archived', 1)
        ->join('customer_credit as t2', 't1.customer_id', '=', 't2.customer_id')
        ->select('t2.*')
        ->where('credit_date', '<', $date)
        ->sum('amount')
    );

    $payments = floatval(
      DB::connection('carmu')
        ->table('customer as t1')
        ->where('t1.archived', 1)
        ->join('customer_payment as t2', 't1.customer_id', '=', 't2.customer_id')
        ->select('t2.*')
        ->where('payment_date', '<', $date)
        ->sum('amount')
    );

    return $credits - $payments;
  }

  protected function getCustomersHistory()
  {
    $result = new Collection();
    $startDate = $this->now->copy()->subDays(30)->startOfDay();
    $format = 'Y-m-d H:i:s';
    try {
      $creditHistory = DB::connection('carmu')
        ->table('customer as t1')
        ->join('customer_credit as t2', 't2.customer_id', '=', 't1.customer_id')
        ->where('t2.credit_date', '>=', $startDate->format($format))
        ->select('t1.customer_id as id', 't1.first_name', 't1.last_name', 't2.credit_date as date', 't2.description', 't2.amount')
        ->orderBy('date')
        ->get();

      $paymentHistory = DB::connection('carmu')
        ->table('customer as t1')
        ->join('customer_payment as t2', 't2.customer_id', '=', 't1.customer_id')
        ->where('t2.payment_date', '>=', $startDate->format($format))
        ->select('t1.customer_id as id', 't1.first_name', 't1.last_name', 't2.payment_date as date', 't2.amount', 't2.cash')
        ->orderBy('date')
        ->get();

      /**
       * Se agregan los creditos al resultado
       */
      foreach ($creditHistory as $credit) {
        $result->push([
          'id' => $credit->id,
          'firstName' => $credit->first_name,
          'lastName' => $credit->last_name,
          'date' => $credit->date,
          'description' => $credit->description,
          'amount' => floatval($credit->amount),
          'isCredit' => true
        ]);
      }

      /**
       * Se agregan los pagos al resultado
       */
      foreach ($paymentHistory as $payment) {
        $description = $payment->cash ? 'Abono en efectivo' : 'Abono por transferencia';
        $result->push([
          'id' => $payment->id,
          'firstName' => $payment->first_name,
          'lastName' => $payment->last_name,
          'date' => $payment->date,
          'description' => $description,
          'amount' => floatval($payment->amount),
          'isCredit' => false
        ]);
      }

      $result = $result->sortByDesc('date');
      // dd($result[0]['date']);
      $result = $result->map(function($x) {
        $x['date'] = Carbon::createFromFormat('Y-m-d H:i:s', $x['date'])->format('d-m-Y');
        $x['lastName'] = $x['lastName'] ? $x['lastName'] : '';
        $fullName = $x['firstName'] . ' ' . $x['lastName'];
        trim($fullName);

        return array_merge($x, ['fullName' => $fullName]);
      });
      $result = $result->values()->all();
    } catch (\Throwable $th) {
      // dd($th);
    }

    return $result;
  }
}
