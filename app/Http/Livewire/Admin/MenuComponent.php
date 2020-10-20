<?php

namespace App\Http\Livewire\Admin;

use App\Models\Admin\Menu;
use App\Rules\Menu\ValidateFieldUrl;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MenuComponent extends Component
{
  public $view = "create";

  public $menuId = '';
  public $name = "";
  public $url = "";
  public $icon = "";

  /**
   * Este sistema de reglas fue la formaque encontre de solucionar 
   * la migracion del antiguo sistema por medio de migraciones 
   * con el fin de agregar una regla para verificar la url. tomado de la
   * documentacion de laravel
   */
  protected function rules()
  {
    return [
      'name' => "required|max:50|unique:menu,name,$this->menuId",
      'icon' => 'nullable|max:50',
      'url' => ['required', 'max:100', function ($attribute, $value, $fail) {
        if ($value !== '#') {
          $menu = Menu::where('url', $value)->where('id', '!=', $this->menuId)->get();
          if (!$menu->isEmpty()) {
            $fail("Está url ya fue asiganda");
          }
        }
      }],
    ];
  }

  protected $attributes = [
    'name' => 'nombre',
    'icon' => 'icono'
  ];

  // protected $foo = $this->name;


  public function render()
  {
    $menus = Menu::getMenus();
    return view('livewire.admin.menu-component', compact('menus'));
  }

  public function resetFields()
  {
    $this->reset(['menuId', 'name', 'url', 'icon', 'view']);
  }

  public function updated($propertyName)
  {
    $this->validateOnly($propertyName, $this->rules(), [], $this->attributes);
  }

  public function store()
  {
    $validateData = $this->validate($this->rules(), [], $this->attributes);
    Menu::create($validateData);
    $this->emit('storedMenu', $this->name);
    $this->resetFields();
  }

  public function edit($id)
  {
    $menu = Menu::find($id, ['id', 'name', 'icon', 'url']);
    if($menu !== null)
    {
      $this->menuId = $menu->id;
      $this->name = $menu->name;
      $this->url = $menu->url;
      $this->icon = $menu->icon;
      $this->view = "edit";
    }else{
      $message = "El recurso que se intenta editar no existe";
      $this->emit('menuNotFound', $message);
    }
  }

  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $menu = Menu::find($this->menuId, ['id']);
    if($menu !== null){
      $menu->update([
        'name' => $this->name,
        'url' => $this->url,
        'icon' => $this->icon
      ]);

      $this->emit('menuUpdated');
      $this->resetFields();
    }else{
      $message = "El recurso que se intenta actualizar no existe";
      $this->emit('menuNotFound', $message);
    }
  }

  public function destroy($id)
  {
    $message = "Menú eliminado!";

    $menu = Menu::find($id);
    if($menu != null){
      $menu->delete();
      $this->emit('menuDeleted', $message);
      $this->resetFields();
    }else{
      $message = "El recurso que se intenta eliminar no existe";
      $this->emit('menuNotFound', $message);
    }
  }
}
