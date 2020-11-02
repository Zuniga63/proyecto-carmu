<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardComponent extends Component
{
  public $now;
  protected $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Obtubre', 'Noviembre', 'Diciembre'];

  public function mount()
  {
    $this->now = Carbon::now();
  }

  public function render()
  {
    $montlyReports = $this->getMontlySales();
    return view('livewire.admin.dashboard-component', compact('montlyReports'));
  }

  public function getMontlySales()
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
}
