<?php

namespace App\Http\Livewire\Admin\Carmu;

use App\Models\OldSystem\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class CustomerProfileComponent extends Component
{
  public ?Customer $customer = null;
  public ?Collection $history = null;
  public ?Collection $pendingCredits = null;
  public ?Collection $expiredCredits = null;
  public ?Collection $creditsPaid = null;
  public ?Collection $successCredits = null;
  public $lastCredit = null;
  public $lastPayment = null;

  // public $customers = null;
  public $search = "";
  protected $queryString = ['search' => ['except' => '']];

  public function getFullNameProperty()
  {
    return $this->customer->first_name . " " . $this->customer->last_name;
  }

  public function getBalanceProperty()
  {
    return $this->customer->credits()->sum('amount') - $this->customer->payments()->sum('amount');
  }

  public function getStateProperty()
  {
    if ($this->balance > 0) {
      if ($this->lastPayment) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->lastPayment->date);
      } else {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->pendingCredits->first()->date);
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

  public function getDateDiffProperty()
  {
    Carbon::setLocale('es_DO');
    if ($this->balance > 0) {
      if ($this->lastPayment) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->lastPayment->date);
        // $diff = $date->diffForHumans(Carbon::now());
        $diff = $date->longRelativeToNowDiffForHumans();
        return "Ultimo abono $diff";
      } else {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->pendingCredits->first()->date);
        // $diff = $date->diffForHumans(Carbon::now());
        $diff = $date->longRelativeToNowDiffForHumans();
        return "Pendiente $diff";
      }
    } else {
      if ($this->lastPayment) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->lastPayment->date);
        // $diff = $date->diffForHumans(Carbon::now());
        $diff = $date->longRelativeToNowDiffForHumans();
        return "Ultimo abono $diff";
      }

      return "No tiene transacciones";
    }
  }

  public function getPaymentStatisticsProperty()
  {
    $expiredCount = $this->expiredCredits->count();
    $successCount = $this->successCredits->count();
    $expiredAmount = 0;
    $successAmount = 0;
    $expiredAverage = 0;
    $successAverage = 0;
    $expiredWeightedAverage = 0;
    $successWeightedAverage = 0;
    $accumulated = 0;
    $count = $expiredCount + $successCount;

    foreach ($this->expiredCredits as $credit) {
      $expiredAmount += floatval($credit->amount);
    }

    $accumulated += $expiredAmount;

    foreach ($this->successCredits as $credit) {
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

  public function getPaymentStatisticsByTimeOfLiveProperty()
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

    foreach ($this->creditsPaid as $credit) {
      $amount = floatval($credit->amount);
      $accumulated += $amount;
      $creditDate = Carbon::createFromFormat('Y-m-d H:i:s', $credit->date);
      $diff = $credit->paid->floatDiffInMonths($creditDate);

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

    foreach ($this->pendingCredits as $credit) {
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

  public function getCustomersProperty()
  {
    $attributes = ['customer_id', 'first_name', 'last_name', 'phone', 'archived'];
    $customers = [];

    if (!empty(trim($this->search))) {
      $customers = Customer::where('first_name', 'like', "%$this->search%")
        ->orWhere('last_name', 'like', "%$this->search%")
        ->orWhere('phone', 'like', "%$this->search%")
        ->orderBy('first_name')
        ->get($attributes);
    } else {
      $customers = Customer::orderBy('first_name')
        ->get($attributes);
    }


    foreach ($customers as $customer) {
      $customer->balance = $customer->credits()->sum('amount') - $customer->payments()->sum('amount');
    }
    return $customers;
  }

  public function mount($id = null)
  {
    if ($id) {
      $this->customer = Customer::findOrFail($id);
      $result = $this->customer->getCreditHistory();
      $this->history = $result['history'];
      $this->pendingCredits = $result['pendingCredits'];
      $this->expiredCredits = $result['expiredCredits'];
      $this->creditsPaid = $result['creditsPaid'];
      $this->lastCredit = $result['lastCredit'];
      $this->lastPayment = $result['lastPayment'];
      $this->successCredits = $result['successCredits'];
    }
  }

  public function render()
  {
    return view('livewire.admin.carmu.customer-profile-component')
      ->layout("admin.carmu.customer-profile.index");
  }

  public function formatDate($date, $format)
  {
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format($format);
  }

  public function formatDateWithFormat($date, $formatIn, $formatOut)
  {
    Carbon::setLocale('es');
    return Carbon::createFromFormat($formatIn, $date)->isoFormat($formatOut);
  }

  public function diffDate($date1, $format1, $date2, $format2)
  {
    $date1 = Carbon::createFromFormat($format1, $date1);
    $date2 = Carbon::createFromFormat($format2, $date2);

    return $date2->shortAbsoluteDiffForHumans($date1);
  }

  public function diffDateFromNow($date, $format)
  {
    return Carbon::createFromFormat($format, $date)->shortRelativeToNowDiffForHumans();
  }
}
