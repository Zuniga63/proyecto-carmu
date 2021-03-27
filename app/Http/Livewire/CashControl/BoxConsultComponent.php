<?php

namespace App\Http\Livewire\CashControl;

use App\Models\CashControl\Box;
use App\Models\CashControl\BoxTransaction;
use App\Models\CashControl\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BoxConsultComponent extends Component
{
  public array $transactionType = [
    'general' => 'General',
    'sale' => 'Ventas',
    'service' => 'Servicios',
    'expense' => 'Gastos',
    'purchase' => 'Compras',
    'credit' => 'Creditos',
    'payment' => 'Abonos',
    'transfer' => 'Transferencia'
  ];
  public int $businessId = 0;
  public int $boxId = 0;
  public string $type = 'all';

  public string $since = '';
  public string $until = '';

  public function getMaxDateProperty()
  {
    return Carbon::now()->format('Y-m-d');
  }

  public function getTransactionsProperty()
  {
    $data = null;
    $transactions = [];
    /** @var Business */
    $business = Business::find($this->businessId);

    if($business){
      $query = null;
      if($this->boxId > 0){
        /** @var Box */
        $box = $business->boxes()->where('id', $this->boxId)->first();
        $query = $box->transactions()->orderBy('transaction_date');
      }else{
        $query =  $business->boxes()->join('box_transaction', 'box.id', '=', 'box_transaction.box_id')->select('box_transaction.*')->orderBy('transaction_date');
      }

      if(!empty($this->since)){
        $date = Carbon::createFromFormat('Y-m-d', $this->since)->startOfDay();
        $query->where('transaction_date', '>=', $date);
      }

      if(!empty($this->until)){
        $date = Carbon::createFromFormat('Y-m-d', $this->until)->endOfDay();
        $query->where('transaction_date', '<', $date);
      }

      if(!empty($this->type) && $this->type !== 'all'){
        $query->where('type', $this->type);
      }

      $data = $query->get();
      $accumulated = 0;
      foreach($data as $record){
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $record->transaction_date)->format('d-m-Y');
        $amount = intval($record->amount);
        $type = $this->transactionType[$record->type];
        $accumulated += $amount;

        $transactions[] = [
          'id' => $record->id,
          'box' => $record->box_id,
          'date' => $date,
          'description' => $record->description,
          'type' => $type,
          'amount' => $amount,
          'accumulated' => $accumulated
        ];
      }
    }

    return $transactions;
  }

  public function render()
  {
    $business = Business::orderBy('id')->pluck('name', 'id');
    $boxes = [];
    if($this->businessId > 0){
      $boxes = Box::where('business_id', $this->businessId)->orderBy('id')->pluck('name', 'id');
    }
    return view('livewire.cash-control.box-consult-component', compact('business', 'boxes'))->layout('livewire.cash-control.box-consult.index');
  }

  public function mount()
  {
    $now = Carbon::now()->format('Y-m-d');
    $this->since = $now;
    $this->until = $now;
  }

  public function updatedBusinessId()
  {
    if($this->businessId > 0){
      $this->boxId = 0;
    }
  }
}
