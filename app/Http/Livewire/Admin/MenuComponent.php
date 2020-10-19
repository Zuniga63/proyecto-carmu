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
  public $name = "Listado actual";
  public $url = "admin/menu";
  public $icon = "fas fa-users";

  // protected $rules = [
  //   'name' => "required|max:50|unique:menu,name,",
  //   'url' => 'required|max:100',
  //   'icon' => 'nullable|max:50'
  // ];

  
  /**
   * En este codigo se utilizó un closure para poder validar el campo url
   * ya que debe hacer una consulta a la base de datos para ello
   */
  protected function rules()
  {
    return [
      'name' => "required|max:50|unique:menu,name,$this->menuId",
      'url' => ['required', 'max:100', function($attribute, $value, $fail){
        if ($value !== '#') {
          $menu = Menu::where('url', $value)->where('id', '!=', $this->menuId)->get();
          if(!$menu->isEmpty()){
            $fail("ya está asignada");
            // $this->addError('url', 'Ya esta asignada');
          }
        }
      }],
      'icon' => 'nullable|max:50'
    ];
  }

  protected $attributes = [
    'name' => 'nombre',
    'icon' => 'icono'
  ];

  // protected $foo = $this->name;


  public function render()
  {
    return view('livewire.admin.menu-component');
  }

  public function resetFields()
  {
    $this->reset(['categoryId', 'name', 'url', 'icon']);
  }

  public function store()
  {

    $this->validate($this->rules(), [], $this->attributes);

    // dd('foo');
    

    // $this->validate($this->rules, [], $this->attributes);
    // Menu::create([
    //   'name' => $this->name,
    //   'url' => $this->url,
    //   'icon' => $this->icon,
    // ]);


    // $this->resetFields();
  }
}
