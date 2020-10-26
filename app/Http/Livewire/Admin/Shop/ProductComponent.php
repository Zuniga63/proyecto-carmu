<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Brand;
use App\Models\Shop\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductComponent extends Component
{
  use WithFileUploads;

  public $view = "create";
  public $productId = null;
  public $brandId = "";
  public $image = null;
  public $actualImage = '';
  public $name = "";
  public $slug = "";
  public $description = "";
  public $price = "";
  public $stock = "";
  public $outstanding = false;
  public $isNew = false;
  public $published = false;

  public function getPathProperty()
  {
    if ($this->image) {
      return $this->image->temporaryUrl();
    }

    return '';
  }

  //---------------------------------------------------
  //  Reglas de validacion y atributos
  //---------------------------------------------------
  protected function rules()
  {
    return [
      'name' => 'required|max:50',
      'slug' => 'required|max:50',
      'description' => 'required',
      'price' => 'required|numeric',
      'stock' => 'required|numeric|min:0|max:255',
      'image' => 'image|max:1024|nullable',
      'outstanding' => 'boolean',
      'isNew' => 'boolean',
      'published' => 'boolean',
    ];
  }

  protected $attributes = [
    'name' => 'nombre',
    'description' => 'descripción',
    'price' => 'precio',
    'image' => 'imagen',
  ];

  //---------------------------------------------------
  //  FUNCIONES DEL RENDERIZADO
  //---------------------------------------------------

  public function render()
  {
    $brands = Brand::orderBy('name')->get(['id', 'name']);
    $products = Product::where('name', 'like', '%' . $this->name . '%')
      ->orderBy('id')
      ->get(['id', 'name', 'img', 'price', 'is_new', 'outstanding', 'published']);

    return view('livewire.admin.shop.product-component', compact('brands', 'products'));
  }

  //---------------------------------------------------
  //  METODOS DEL CRUD
  //---------------------------------------------------
  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $imagePath = null;

    DB::beginTransaction();
    try {
      $imagePath = $this->storeImage();

      Product::create([
        'brand_id' => 0 >= $this->brandId ? null : $this->brandId,
        'name' => $this->name,
        'slug' => $this->slug,
        'img' => $imagePath,
        'description' => $this->description,
        'price' => $this->price,
        'stock' => $this->stock,
        'outstanding' => $this->outstanding,
        'is_new' => $this->isNew,
        'published' => $this->published,
      ]);
      $this->resetFields();
      $this->emit('stored');
      DB::commit();
    } catch (\Exception $ex) {
      $this->deleteImage($imagePath);
      DB::rollBack();
    }
  }

  public function destroy($id)
  {
    $product = $this->findProduct($id);
    if($product){
      $this->deleteImage($product->img);
      $product->delete();
      $this->emit('deleted');
      $this->resetFields();
    }
  }

  public function edit($id)
  {
    $product = $this->findProduct($id);
    if($product){
      $this->resetFields();
      $this->view         = "edit";
      $this->productId    = $product->id;
      $this->brandId      = $product->brand_id ? $product->brand_id : 0;
      $this->actualImage  = $product->img;
      $this->name         = $product->name;
      $this->slug         = $product->slug;
      $this->description  = $product->description;
      $this->price        = $product->price;
      $this->stock        = $product->stock;
      $this->outstanding  = $product->outstanding;
      $this->isNew        = $product->is_new;
      $this->published    = $product->published;
    }
  }

  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $imagePath  = null;
    $product    = $this->findProduct($this->productId);

    if($product){
      DB::beginTransaction();
      try {
        $imagePath = $this->storeImage();
  
        $product->update([
          'brand_id' => 0 >= $this->brandId ? null : $this->brandId,
          'name' => $this->name,
          'slug' => $this->slug,
          'img' => $imagePath ? $imagePath : $this->actualImage,
          'description' => $this->description,
          'price' => $this->price,
          'stock' => $this->stock,
          'outstanding' => $this->outstanding,
          'is_new' => $this->isNew,
          'published' => $this->published,
        ]);

        //Procedo a eliminar la imagen antigua
        if($imagePath){
          $this->deleteImage($this->actualImage);
        }

        $this->resetFields();
        $this->emit('updated');
        DB::commit();
      } catch (\Exception $ex) {
        $this->deleteImage($imagePath);
        DB::rollBack();
      }//end try-catch
    }//End if

  }//end method

  public function changeState($id, $feature, $value)
  {
    $product = $this->findProduct($id, ['id', 'is_new', 'outstanding', 'published']);
    $value = $value ? true : false;
    if($product){
      switch ($feature) {
        case 'isNew': {
            $product->update(['is_new' => $value]);
            $this->emit('stateUpdated');
          }
          break;
        case 'outstanding': {
            $product->update(['outstanding' => $value]);
            $this->emit('stateUpdated');
          }
          break;
        case 'published': {
            $product->update(['published' => $value]);
            $this->emit('stateUpdated');
          }
          break;
      }
    }
  }

  //---------------------------------------------------
  //  UTILIDADES
  //---------------------------------------------------
  public function resetFields()
  {
    $this->reset('view', 'productId', 'brandId', 'image', 'actualImage', 'name', 'slug', 'description', 'price', 'stock', 'outstanding', 'isNew', 'published');
  }

  public function findProduct($id, $columns=null)
  {
    $result = null;

    if($columns){
      $result = Product::find($id, $columns);
    }else{
      $result = Product::find($id);
    }

    if($result === null){
      $this->emit('notFound');
    }
    
    return $result;
  }

  protected function deleteImage($imagePath)
  {
    if($imagePath){
      $exist = Storage::disk('public')->exists($imagePath);
      if($exist){
        Storage::disk('public')->delete($imagePath);
      }
    }
  }

  protected function storeImage()
  {
    $path = null;
    if ($this->image) {
      $path = $this->image->store('img/products', 'public');
    }

    return $path;
  }  
}
