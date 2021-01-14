<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardComponent extends Component
{
  public Carbon $now;
  public $year;
  public $month;
  public $tremester;
  public $semester;
  public $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
  public $sales = [];

  public function mount()
  {
    $now = Carbon::now()->timezone("America/Bogota");
    $this->now = $now;
    $this->year = $now->year;
    $this->month = $now->month;
    $this->tremester = ceil($this->month / 3.0);
    $this->semester = ceil($this->month / 6.0);
    $this->sales = $this->getSales();
  }

  public function render()
  {
    // dd($this->getSales());
    $data = $this->getData();
    // $montlyReports = $this->getMontlyReports();
    $categories = $this->getMontlyReportsByCategories();
    $months = $this->months;
    $creditEvolutions = $this->creditEvolution();
    return view('livewire.admin.dashboard-component', compact('categories', 'months', 'creditEvolutions', 'data'))
      ->layout("admin.dashboard.index");
  }

  public function getData()
  {
    $now = Carbon::now()->timezone("America/Bogota");
    $year = $now->year;
    $month = $now->month;
    // $month = 7;
    $tremester = ceil($month / 3.0);
    $semester = ceil($month / 6.0);

    $data = [
      // 'monthlyReports' => $this->getMontlyReports(),
      'categories' => $this->getCategories(),
      'months' => $this->months,
      'creditEvolutions' => $this->creditEvolution(),
      'sales' => $this->getSales(),
      'month' => $month,
      'tremester' => $tremester,
      'semester' => $semester,
      'year' => $year
    ];

    return $data;
  }

  public function getMontlyReports()
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

  public function getMontlyReportsByCategories()
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

  public function sortByAmount($a, $b)
  {
    return $a['amount'] - $b['amount'];
  }

  public function getMontlyReportsByCategory($id = 4)
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

  public function creditEvolution()
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
   * Este metodo se encarga de recuperar las ventas 
   * de los ultimos dos años, clasificandolos por meses y
   * luego por días
   */
  public function getSales()
  {
    $now = Carbon::now()->timezone("America/Bogota");
    $thisYear = $now->copy()->startOfYear();
    $lastYear = $thisYear->copy()->subYear()->startOfYear();

    return [
      $this->getSaleOfYear($lastYear),
      $this->getSaleOfYear($thisYear)
    ];
  }

  public function getSaleOfYear(Carbon $startDate)
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
}
