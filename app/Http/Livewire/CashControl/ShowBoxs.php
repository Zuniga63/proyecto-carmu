<?php

namespace App\Http\Livewire\CashControl;

use App\Models\CashControl\Box;
use Livewire\Component;

class ShowBoxs extends Component
{
  public ?int $boxId = null;


  public function getBoxsProperty()
  {
    $boxs = [];
    if(empty($this->boxId)){
      $boxData = Box::orderBy('id')->with(['business', 'cashier'])->get();
      
      foreach ($boxData as $data) {
        $balance = $data->transactions()->sum('amount');
        $query = $data->transactions()->where('transaction_date', '>=', $data->closing_date);
        $income = $query->where('amount', '>', 0)->sum('amount');
        $expense = $query->where('amount', '<', 0)->sum('amount');

        $boxs [] = [
          'id'        => $data->id,
          'name'      => $data->name,
          'base'      => round($data->base,2),
          'balance'   => round($balance, 2),
          'income'    => round($income, 2),
          'expense'   => round($expense, 2),
          'cashier'   => $data->cashier ? $data->cashier->name : 'No asignado',
          'business'  => $data->business ? $data->business->name : '',
        ];
      }
    }

    return $boxs;
  }

  public function render()
  {
    return view('livewire.cash-control.show-boxs')->layout('livewire.cash-control.show-box.index');
  }

  public function mount($id = null){
    //TODO
  }
}
