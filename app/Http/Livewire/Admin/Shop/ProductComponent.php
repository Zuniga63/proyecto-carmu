<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Brand;
use App\Models\Shop\Category;
use App\Models\Shop\Color;
use App\Models\Shop\Product;
use App\Models\Shop\Size;
use App\Models\Shop\Tag;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

use function PHPUnit\Framework\isNull;

class ProductComponent extends Component
{
  use WithFileUploads;

  /**
   * Sirve para poder saltar entre el formulario para crear
   * y el formulario para editar
   */
  public $view = "create";
  public $productId = null;
  
  //-------------------------------------------
  //  COLECCIÓN DE DATOS
  //-------------------------------------------
  public $allCategories = null;
  public $allTags = null;
  public $allBrands = null;
  public $allSize = null;
  public $allColors = null;

  //-------------------------------------------
  //  IMAGE PROPERTY
  //-------------------------------------------
  /**
   * Corresponde a los datos de la imagen que
   * se desea guardar en el servidor
   */
  public $image = null;
  /**
   * La direccion de la imagen que está asiganada al producto
   * que se desea actualizar.
   */
  public $actualImage = '';
  /**
   * Esta propiedad se activa cuando se desea eliminar la
   * imgen asignada al producto actual
   */
  public $deleteActualProductImage = false;

  /**
   * Retorna la direccion de la imagen temporal subida el aservidor;
   * si dicha imagen no ha sido subida entonces retorna la dirección de
   * una imagen generica
   */
  public function getImagePathProperty()
  {
    if ($this->image) {
      return $this->image->temporaryUrl();
    }

    return asset('storage/img/products/no-image-available.png');
  }

  /**
   * Retorna la dirección donde está almacenada la imagen del producto 
   * a editar. Si el producto no tiene imagen entonces retorna una
   * imagen generica.
   */
  public function getActualProductImagePathProperty()
  {
    if ($this->actualImage) {
      return asset('storage/' . $this->actualImage);
    }

    return asset('storage/img/products/no-image-available.png');
  }

  //-------------------------------------------
  //  GENERAL PROPERTY
  //-------------------------------------------
  public $name = "";
  public $slug = "";
  public $description = "";

  //-------------------------------------------
  //  REFERENCE AND PRICE
  //-------------------------------------------
  public $ref = null;
  public $barcode = null;
  public $price = null;
  //-------------------------------------------
  // FEATURE PROPERTIES
  //-------------------------------------------
  public $sizeId = "";
  public $colorId = "";
  public $colorHex = "";
  public $brandId = "";

  public $stock = 0;               //Deprecated
  public $outstanding = false;      //Deprecated
  public $isNew = false;            //Deprecated
  public $published = false;        //Deprecated

  //-------------------------------------------
  // CATEGORIES AND TAGS
  //-------------------------------------------

  /**
   * Es el arreglo de id's de las etiquetas asociadas al producto
   * que se desea crear o editar
   */
  public $tags = [];

  public $mainCategoryId = 0;
  public $subcategoryId = 0;

  public $categoryRoute = [];
  public $actualCategory = null;

  public $temporalTags = "";
  public $temporalCategories = "";

  //---------------------------------------------------
  //  Reglas de validacion y atributos
  //---------------------------------------------------
  protected function rules()
  {
    return [
      'image' => 'image|max:1024|nullable',
      'name' => 'required|max:50',
      'slug' => 'required|max:50',
      'description' => 'required',
      'ref' => 'nullable|max:50',
      'barcode' => 'nullable|max:255|unique:product,barcode,' . $this->productId,
      'price' => 'required|numeric',
      'brandId' => 'nullable|numeric|min:1|exists:brand,id',
      'sizeId' => 'nullable|numeric|min:1|exists:size,id',
      'colorId' => 'nullable|numeric|min:1|exists:color,id',
      'stock' => 'required|numeric|min:0|max:255',
      'outstanding' => 'boolean',
      'isNew' => 'boolean',
      'published' => 'boolean',
      'tags' => ['array', function($attribute, $value, $fail){
        foreach($value as $tagId){
          if(Tag::where('id', $tagId)->doesntExist()){
            $fail('Existe una etiqueta invalida!');
            break;
          }
        }
      }],
      'categoryRoute' => ['array', function($attribute, $value, $fail){
        if(count($value) > 0){
          foreach($value as $category){
            $exist = Category::where('id', $category['id'])
              ->where('father_id', $category['fatherId'])
              ->exists();
            if(!$exist){
              $categoryName = $category['name'];
              $fail("La categoría $categoryName no existe!");
              break;
            }
          }
        }else{
          $fail('Se debe elegir una categoría principal');
        }
      }],
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

  public function mount()
  {
    $this->allCategories = Category::getCategories();
    $this->allBrands = Brand::orderBy('name')->get(['id', 'name'])->toArray();
    $this->allTags = Tag::orderBy('name')->get(['id', 'name'])->toArray();
    $this->allSize = Size::orderBy('value')->get(['id', 'value'])->toArray();
    $this->allColors = Color::orderBy('name')->get(['id', 'name', 'hex'])->toArray();
  }

  public function render()
  {
    $products = Product::where('name', 'like', '%' . $this->name . '%')
      ->orderBy('id')
      ->get(['id', 'name', 'img', 'price', 'is_new', 'outstanding', 'published']);

    return view('livewire.admin.shop.product-component', compact('products'))
      ->layout('admin.shop.product.index');
  }

  //---------------------------------------------------
  //  METODOS DEL CRUD
  //---------------------------------------------------
  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    // dd($this);
    $imagePath = null;

    DB::beginTransaction();
    try {
      $imagePath = $this->storeImage();

      $product = Product::create([
        'brand_id' => $this->brandId ? $this->brandId : null,
        'color_id' => $this->colorId ? $this->colorId : null,
        'size_id' => $this->sizeId ? $this->sizeId : null,
        'img' => $imagePath,
        'name' => $this->name,
        'slug' => $this->slug,
        'description' => $this->description,
        'ref' => empty($this->ref) ? null : $this->ref,
        'barcode' => empty($this->barcode) ? null : $this->barcode,
        'price' => $this->price,
        'stock' => $this->stock,
        'outstanding' => $this->outstanding,
        'is_new' => $this->isNew,
        'published' => $this->published,
      ]);

      /**
       * Como la validación no dejará pasar un arreglo vacio de 
       * categorias es seguro utilizar un mapeado de los mismos para porder
       * recuperar los ids y luego relacionar con el producto recien creado.
       */
      $product->categories()->attach(array_map(fn($category) => $category['id'], $this->categoryRoute));

      /**
       * Se crea la relación entre el producto y sus etiquetas
       */
      $product->tags()->attach($this->tags);

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
    if ($product) {
      $this->deleteImage($product->img);
      $product->delete();
      $this->emit('deleted');
      $this->resetFields();
    }
  }

  public function edit($id)
  {
    $product = $this->findProduct($id);
    // dd($product->categories);
    if ($product) {
      $this->resetFields();
      $this->view         = "edit";
      $this->productId    = $product->id;
      $this->actualImage  = $product->img;
      $this->name         = $product->name;
      $this->slug         = $product->slug;
      $this->description  = $product->description;
      $this->ref          = $product->ref;
      $this->barcode      = $product->barcode;
      $this->price        = $product->price;
      $this->brandId      = $product->brand_id ? $product->brand_id : '';
      $this->sizeId       = $product->size_id ? $product->size_id : '';
      $this->colorId      = $product->color_id ? $product->color_id : '';
      $this->stock        = $product->stock;
      $this->outstanding  = $product->outstanding;
      $this->isNew        = $product->is_new;
      $this->published    = $product->published;

      /**
       * Se recupera el codigo de color del producto seleccionado
       */
      if(!empty($this->colorId)){
        $colorId = intval($this->colorId);
        $key = array_key_first(array_filter($this->allColors, fn($color) => intval($color['id']) == $colorId));
        $this->colorHex = $this->allColors[$key]['hex'];
      }

      /**
       * Se recuperan los Ids de las etiquetas asociadas a este producto
       */
      if(count($product->tags) > 0){
        $temporal = "";
        foreach($product->tags as $tag){
          $temporal .= "\"" . $tag->name . "\" ";
        }

        $this->temporalTags = $temporal;
      }else{
        $this->temporalTags = "No tiene etiquetas";
      }

      /**
       * Ahora se recuperan las categorías del producto
       */
      if(count($product->categories) > 0){
        $temporal = "";
        foreach($product->categories as $data){
          $temporal .= "\"" . $data->name . "\" ";
        }
        $this->temporalCategories = $temporal;
      }else{
        $this->temporalCategories ="No tiene categorías";
      }

      $this->emit('edit');

      // /**
      //  * Codigo temporal para recuperar la categoría
      //  */
      // $data =  DB::table('category_has_product')->where('product_id', $id)->first(['category_id']);
      // $this->categoryId = $data ? $data->category_id : '';
    }
  }

  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $imagePath  = null;
    $product    = $this->findProduct($this->productId);

    if ($product) {
      DB::beginTransaction();
      try {
        $imagePath = $this->storeImage();

        $product->update([
          'brand_id' => $this->brandId ? $this->brandId : null,
          'color_id' => $this->colorId ? $this->colorId : null,
          'size_id' => $this->sizeId ? $this->sizeId : null,
          'img' => $imagePath ? $imagePath : $this->actualImage,
          'name' => $this->name,
          'slug' => $this->slug,
          'description' => $this->description,
          'ref' => empty($this->ref) ? null : $this->ref,
          'barcode' => empty($this->barcode) ? null : $this->barcode,
          'price' => $this->price,
          'stock' => $this->stock,
          'outstanding' => $this->outstanding,
          'is_new' => $this->isNew,
          'published' => $this->published,
        ]);

        //Reasigno las categorías
        $product->categories()->detach();
        $product->categories()->attach(array_map(fn($category) => $category['id'], $this->categoryRoute));

        //Ahora Reasigno las etiquetas
        $product->tags()->detach();
        $product->tags()->attach($this->tags);

        //Procedo a eliminar la imagen antigua
        if ($imagePath && $this->actualImage) {
          $this->deleteImage($this->actualImage);
        }

        $this->resetFields();
        $this->emit('updated');
        DB::commit();
      } catch (\Exception $ex) {
        $this->deleteImage($imagePath);
        DB::rollBack();
      } //end try-catch
    } //End if

  } //end method

  public function changeState($id, $feature, $value)
  {
    $product = $this->findProduct($id, ['id', 'is_new', 'outstanding', 'published']);
    $value = $value ? true : false;
    if ($product) {
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
    $this->reset('view', 'productId', 'brandId', 'sizeId', 'colorId', 'colorHex', 'image', 'actualImage', 'name', 'slug', 'description', 'ref', 'barcode',  'price', 'stock', 'outstanding', 'isNew', 'published', 'categoryRoute', 'actualCategory', 'mainCategoryId', 'subcategoryId', 'tags');
    $this->emit('reset');
  }

  public function findProduct($id, $columns = null)
  {
    $result = null;

    if ($columns) {
      $result = Product::with(['categories', 'tags'])
        ->find($id, $columns);
      } else {
      $result = Product::with(['categories', 'tags'])
        ->find($id);
    }

    if ($result === null) {
      $this->emit('notFound');
    }

    return $result;
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

  /**
   * Este metodo se encarga de remover la imagen subida...
   * o de indicarle al modelo que debe eliminar la imagen del
   * producto con la siguiente actualización
   */
  public function removeImage()
  {
    if ($this->image || $this->actualImage) {
      if ($this->image) {
        //Esto hace que la propiedad computada se actualice
        $this->image = null;
      } else {
        $this->deleteActualProductImage = true;
      }
    }
  }

  public function undoImageChange()
  {
    $this->deleteActualProductImage = false;
  }
}
