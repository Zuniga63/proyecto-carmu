<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Size;
use Livewire\Component;

class SizeComponent extends Component
{
  public $view = 'create';
  public $sizeId = null;
  public $sizeValue = '';

  protected function rules()
  {
    return [
      'sizeValue' => 'required|max:5|unique:size,value,' . $this->sizeId,
    ];
  }

  protected $attributes = ['sizeValue' => 'Talla'];


  public function render()
  {
    $sizes = Size::orderBy('value')->pluck('value', 'id')->toArray();
    return view('livewire.admin.shop.size-component', compact('sizes'))
      ->layout('admin.shop.size.index');
  }

  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    try {
      Size::create([
        'value' => trim($this->sizeValue)
      ]);

      $this->emit('sizeStored');
      // $this->reset('sizeValue', 'view', 'sizeId');
      $this->resetFields();
    } catch (\Throwable $th) {
      $this->emit('error');
    }
  }

  public function edit($id)
  {
    try {
      $size = Size::find($id, ['id', 'value']);
      if ($size) {
        $this->sizeId = $id;
        $this->sizeValue = $size->value;
        $this->view = "edit";
        $this->resetErrorBag();
      } else {
        $this->emit('sizeNotFound');
        $this->resetFields();
      }
    } catch (\Throwable $th) {
      $this->emit('error');
    }
  }

  public function updateSize()
  {
    $this->validate($this->rules(), [], $this->attributes);

    if ($this->sizeId) {
      try {
        $size = Size::find($this->sizeId, ['id', 'value']);
        if ($size) {
          $size->value = $this->sizeValue;
          $size->save();
          $this->emit('sizeUpdated');
        } else {
          $this->emit('sizeNotFound');
        }
      } catch (\Throwable $th) {
        $this->emiit('error');
      }
    }
    $this->resetFields();
  }

  public function destroy($id)
  {
    try {
      $size = Size::find($id);
      if($size){
        $size->delete();
        $this->emit('sizeDeleted');
      }else{
        $this->emit('sizeNotFound');
      }
    } catch (\Throwable $th) {
      $this->emit('error');
    }
    $this->resetFields();
  }

  public function resetFields()
  {
    $this->reset('sizeValue', 'view', 'sizeId');
  }
}
