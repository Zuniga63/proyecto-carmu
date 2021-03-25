<?php

namespace App\Http\Livewire\SonDeCuatro;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
  public function render()
  {
    $products = $this->getProducts();
    return view('livewire.son-de-cuatro.dashboard', compact('products'));
  }

  protected function getProducts()
  {
    $data = DB::table('sondecuatro')->get();
    $products = [];

    foreach ($data as $record) {
      $imagePaht = 'storage/img/products/no-image-available.png';
      if ($record->img) {
        $imagePaht = "storage/" . $record->img;
      }

      $price = intval($record->price);
      $expense = intval($record->expense);
      $utility = $expense > 0 ? ($price / $expense) - 1 : 0;
      $utility = round($utility, 4) * 100;

      $products[] = [
        'name' => $record->name,
        'img' => $imagePaht,
        'price' => $price,
        'expense' => $expense,
        'utility' => $utility
      ];
    }

    return $products;
  }
}
