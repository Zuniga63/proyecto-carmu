<?php

namespace App\Http\Livewire;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Livewire\Component;
// use Livewire\WithPagination;

class PermissionComponent extends Component
{
  // use WithPagination;
  /**
   * Este parametro es para intercarlar 
   * entre el formulario de crear y actualizar
   */
  public $view = "create";
  public $placeholder = "Escribe el nombre aquí";

  /**
   * Campos relacionados con el estado del componente
   */
  public $permission_id, $name, $slug;

  // protected $listener = ['triggerDelete' => 'destroy'];

  /**
   * Este metodo se encarga de crear el slug del permiso
   * y tambien de guardar los datos en la base de datos
   * despues de validarlos. Al final emite un evento 
   * notificando que se ha guardado.
   */
  public function store()
  {
    $this->slug = str_replace(' ', '-', mb_strtolower($this->name));
    
    $this->fieldValidation();

    $permission =  Permission::create([
      'name' => $this->name,
      'slug' => $this->slug,
    ]);

    //Se monta el formulario para editar
    $this->edit($permission->id);
    $this->emit('permissionCreated');
  }

  /**
   * Contiene las reglas de validacion para los
   * dos campos que van a ser agregados
   */
  protected function fieldValidation()
  {
    $this->validate([
      'name' => "required|max:50|unique:permission,name," . $this->permission_id,
      'slug' => 'required|max:50|unique:permission,slug,' . $this->permission_id,
    ], [], ['name' => 'nombre']);
  }

  public function render()
  {
    $permissions = Permission::orderBy('name')->with('roles')->get();
    $roles = Role::orderBy('id')->get();
    $permissionRole = Permission::with('roles')
      ->get()
      ->pluck('roles', 'id')
      ->toArray();
      // dd($permissionRole);
    return view('livewire.permission-component', compact('permissions', 'roles', 'permissionRole'));
  }

  public function destroy($id)
  {
    Permission::destroy($id);
    $this->emit('permissionDestroyed');
  }

  public function edit($id)
  {
    $permission = Permission::find($id);
    $this->name = $permission->name;
    $this->slug = $permission->slug;
    $this->permission_id = $id;
    $this->view = 'edit';
  }

  public function update()
  {
    $this->fieldValidation();
    $permission = Permission::find($this->permission_id);
    $permission->update([
      'name' => $this->name,
      'slug' => str_replace(' ', '-', mb_strtolower($this->name))
    ]);
    $this->default();
  }

  public function permissionAssignment($permissionId, $roleId, $checked)
  {
    $permission = new Permission();
    if($checked)
    {
      $permission->find($permissionId)->roles()->attach($roleId);
      $this->emit('permissionAssigned');
    }else{
      $permission->find($permissionId)->roles()->detach($roleId);
      $this->emit('permissionRemoved');
    }
  }

  public function default()
  {
    $this->permission_id = '';
    $this->name = '';
    $this->slug = '';
    $this->view = 'create';
  }
}
