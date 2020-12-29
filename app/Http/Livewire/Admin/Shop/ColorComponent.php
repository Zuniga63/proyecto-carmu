<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Color;
use Livewire\Component;

class ColorComponent extends Component
{
  public $view = 'create';
  public $colorId = null;
  public $colorName = '';
  public $colorHex = '#000000';

  protected function rules()
  {
    return [
      'colorName' => 'required|max:20|unique:color,name,' . $this->colorId,
      'colorHex' => 'required|max:7|unique:color,hex,' . $this->colorId,
    ];
  }

  protected $attributes = [
    'colorName' => 'Nombre',
    'colorHex' => 'Codigo de Color'
  ];


  public function render()
  {
    $colors = Color::all(['id', 'name', 'hex'])->toArray();
    return view('livewire.admin.shop.color-component', compact('colors'))
      ->layout('admin.shop.colors.index');
  }

  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    try {
      Color::create([
        'name' => $this->colorName,
        'hex' => $this->colorHex,
      ]);

      $this->emit('colorStored');
      $this->resetFields();
    } catch (\Throwable $th) {
      $this->emit('error');
    }
  }

  public function edit($id)
  {
    try {
      $color = Color::find($id, ['id','name', 'hex']);
      if ($color) {
        $this->colorId = $color->id;
        $this->colorName = $color->name;
        $this->colorHex = $color->hex;
        $this->view = 'edit';
      } else {
        $this->emit('colorNotFound');
        $this->resetFields();
      }
    } catch (\Throwable $th) {
      $this->emit('error');
    }
  }

  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);

    if ($this->colorId) {
      try {
        $color = Color::find($this->colorId, ['id', 'name', 'hex']);
        if ($color) {
          $color->name = trim($this->colorName);
          $color->hex = $this->colorHex;
          $color->save();
          $this->emit('colorUpdated');
        } else {
          $this->emit('colorNotFound');
        }
      } catch (\Throwable $th) {
        $this->emit('error');
      }
    }

    $this->resetFields();
  }

  public function destroy($id)
  {
    try {
      $color = Color::find($id);
      if($color){
        $color->delete();
        $this->emit('colorDeleted');
      }else{
        $this->emit('colorNotFound');
      }
    } catch (\Throwable $th) {
      $this->emit('error');
    }

    $this->resetFields();
  }

  public function resetFields()
  {
    $this->reset('view', 'colorName', 'colorHex', 'colorId');
  }
}