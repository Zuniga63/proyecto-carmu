<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Category;
use Livewire\Component;

class CategoryComponent extends Component
{
  public $view = "create";
  public $categoryId = null;
  public $name = "";
  public $slug = "";
  public $icon = null;

  protected $rules = [
    'name' => "required|min:4|max:50|",
    'slug' => 'required|min:4|max:50|',
  ];

  protected $attributes = [
    'name' => 'nombre'
  ];

  public function render()
  {
    $categories = Category::getCategories();
    return view('livewire.admin.shop.category-component', compact('categories'));
  }

  public function resetFields()
  {
    $this->reset([
      'categoryId', 'name', 'slug', 'icon', 'view',
    ]);
  }
    
  /**
   * Almacena los datos de la categorías en la base de datos
   * y emite un evento con el nombre de la categoría creada para 
   * luego resetear los valores del formulario.
   */
  public function store()
  {
    $this->slug = str_replace(' ', '-', mb_strtolower($this->slug));
    $this->icon = empty($this->icon) ? null : $this->icon;
    $this->validate($this->rules, [], $this->attributes);

    Category::create([
      'name' => $this->name,
      'slug' => $this->slug,
      'icon' => $this->icon,
    ]);

    $this->emit('categoryStored', $this->name);
    $this->resetFields();
  }

  /**
   * Si la cateogía se encuentra en la base de datos
   * procede a montar los valores de la categoría en 
   * los parametros publicos
   */
  public function edit($id)
  {
    $category = Category::find($id, ['id', 'name', 'slug', 'icon']);
    if($category !== null)
    {
      $this->categoryId = $category->id;
      $this->name = $category->name;
      $this->slug = $category->slug;
      $this->icon = $category->icon;
      $this->view = "edit";
    }else{
      $message = "El recurso que se intenta editar no existe";
      $this->emit('categoryNotFound', $message);
    }
  }

  public function update()
  {
    $this->validate($this->rules, [], $this->attributes);
    $category = Category::find($this->categoryId);
    if($category !== null){
      $category->update([
        'name' => $this->name,
        'slug' => $this->slug,
        'icon' => $this->icon,
      ]);

      $this->emit('categoryUpdated');
      $this->resetFields();
    }else{
      $message = "El recurso que se intenta actualizar no existe";
      $this->emit('categoryNotFound', $message);
    }
  }

  /**
   * Elimina el registro y emite un evento
   * categoryDeleted si fue satisfactorio o un evento
   * categoryNotFound si el id no está en la BD
   */
  public function destroy($id)
  {
    $message = "¡Categoría eliminada!";

    $category = Category::find($id);
    // dd($id);
    // dd($category != null);
    if($category != null){
      $category->delete();
      $this->emit('categoryDeleted', $message);
    }else{
      $message = "El recurso que se intenta eliminar no existe";
      $this->emit('categoryNotFound', $message);
    }
  }

  
  /**
   * Actualiza la estructura de las categorías
   * en la base de datos
   */
  public function saveOrder($categories)
  {
    $category = new Category();
    $category->saveOrder($categories);
    $this->emit('categoryOrderSaved');
  }
}
