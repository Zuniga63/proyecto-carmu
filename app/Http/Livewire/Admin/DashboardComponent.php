<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardComponent extends Component
{
  public $now;
  protected $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

  public function mount()
  {
    $this->now = Carbon::now();
  }

  public function render()
  {
    $montlyReports = $this->getMontlyReports();
    $categories = $this->getMontlyReportsByCategories();
    $months = $this->months;
    $creditEvolutions = $this->creditEvolution();
    return view('livewire.admin.dashboard-component', compact('montlyReports', 'categories', 'months', 'creditEvolutions'))
      ->layout("admin.dashboard.index");
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

    for ($index=0; $index < 12; $index++) { 
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
}
