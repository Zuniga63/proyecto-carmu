<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Brand;
use Livewire\Component;

class BrandComponent extends Component
{
  public $view = 'create';
  public $brandId = null;
  public $name = '';
  public $slug = '';

  protected function rules()
  {
    return [
      'name' => 'required|max:20|unique:brand,name,' . $this->brandId,
      'slug' => 'required|max:20|unique:brand,slug,' . $this->brandId,
    ];
  }

  protected $attributes = [ 'name' => 'nombre'];

  public function updated($propertyName)
  {
    $this->validateOnly($propertyName, $this->rules(), [], $this->attributes);
  }

  public function render()
  {
    $brands = Brand::get(['id', 'name', 'slug']);
    return view('livewire.admin.shop.brand-component', compact('brands'));
  }

  public function resetFields()
  {
    $this->reset('name', 'slug', 'view', 'brandId');
  }

  public function findBrand($id)
  {
    $brand = Brand::find($id, ['id', 'name', 'slug']);
    if($brand === null){
      $this->emit('brandNotFound');
    }

    return $brand;
  }

  public function store()
  {
    // dd($this->name);
    $this->validate($this->rules(), [], $this->attributes);

    Brand::create([
      'name' => trim($this->name),
      'slug' => trim($this->slug)
    ]);

    $this->resetFields();

    $this->emit('brandStored');
  }

  public function destroy($id)
  {
    $brand = $this->findBrand($id);
    if($brand)
    {
      $brand->delete();
      $this->emit('brandDeleted');
      $this->resetFields();
    }
  }

  public function edit($id)
  {
    $brand = $this->findBrand($id);

    if($brand){
      $this->brandId = $brand->id;
      $this->name = $brand->name;
      $this->slug = $brand->slug;
      $this->view = "edit";
    }else{
      $this->resetFields();
    }
  }

  public function update()
  {
    $brand = $this->findBrand($this->brandId);
    if($brand){
      $validation = $this->validate($this->rules(), [], $this->attributes);
      $brand->update($validation);
      $this->emit("brandUpdated");
    }

    $this->resetFields();
  }
}
