<?php

namespace App\Http\Livewire\SonDeCuatro;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductsComponent extends Component
{
  use WithFileUploads;

  public string $state = 'creating';

  public ?int $productId = null;
  public string $name = '';
  public int $expense = 0;
  public int $price = 0;
  public $image = null;
  public ?string $actualImagePath = null;

  protected function rules()
  {
    return [
      'name' => 'required|string|min:3|max:50',
      'expense' => 'required|numeric|min:0',
      'price' => 'required|numeric|min:0',
      'image' => 'nullable|image|max:1024'
    ];
  }


  /**
   * Retorna la direccion de la imagen temporal subida el aservidor;
   * si dicha imagen no ha sido subida entonces retorna la dirección de
   * una imagen generica
   */
  public function getImagePathProperty()
  {
    if ($this->actualImagePath) {
      return $this->actualImagePath;
    } else if ($this->image) {
      return $this->image->temporaryUrl();
    }

    return asset('storage/img/products/no-image-available.png');
  }

  public function render()
  {
    $products = DB::table('sondecuatro')->get(['id', 'name']);
    return view('livewire.son-de-cuatro.products-component', compact('products'))->layout('livewire.son-de-cuatro.admin.index');
  }

  /**
   * Guarda la imagen en una ubicación publica y retorna 
   * el path. Si no hay imagen retorna null
   */
  protected function storeImage()
  {
    $path = null;
    if ($this->image) {
      $path = $this->image->store('img/products', 'public');
    }

    return $path;
  }

  protected function deleteImage($imagePath)
  {
    if ($imagePath) {
      $exist = Storage::disk('public')->exists($imagePath);
      if ($exist) {
        Storage::disk('public')->delete($imagePath);
      }
    }
  }

  protected function alert(?string $title = null, ?string $type = 'warning', ?string $message = null)
  {
    $this->emit('alert', $title, $message, $type);
  }

  protected function store()
  {
    $this->validate();
    $imagePath = null;
    try {
      $imagePath = $this->storeImage();

      DB::beginTransaction();
      DB::table('sondecuatro')->insert([
        'name' => $this->name,
        'price' => $this->price,
        'expense' => $this->expense,
        'img' => $imagePath,
      ]);

      
      DB::commit();
      $this->resetFields();
      $this->alert('Producto almacenado', 'success');
    } catch (\Throwable $th) {
      $this->deleteImage($imagePath);
      throw $th;
    }
  }

  protected function update()
  {
    $this->validate();

    DB::table('sondecuatro')->where('id', $this->productId)->update([
      'name' => $this->name,
      'price' => $this->price,
      'expense' => $this->expense
    ]);

    $this->alert('Producto actualizado', 'info');
    $this->resetFields();
  }

  public function edit($id){
    $product = DB::table('sondecuatro')->find($id);
    if($product){
      $this->productId = $product->id;
      $this->name = $product->name;
      $this->expense = $product->expense;
      $this->price = $product->price;
      $this->actualImagePath = asset("storage/" . $product->img);
      $this->state = 'editing';
      $this->emit('updateAmount', $this->price, $this->expense);
    }
  }

  public function submit()
  {
    if ($this->state === 'creating') {
      $this->store();
    } else if ($this->state === 'editing') {
      $this->update();
    }
  }

  public function resetFields()
  {
    $this->reset('state', 'name', 'image', 'expense', 'price', 'actualImagePath');
    $this->emit('reset');
  }
}
