<?php

namespace App\Http\Livewire\Admin;

use App\Models\Admin\Role;
use Livewire\Component;

class RoleComponent extends Component
{
  public $view = "create";
  public $roleId = null;
  public $name = "";

  protected function rules()
  {
    return [
      'name' => 'required|max:50|unique:role,name,' . $this->roleId
    ];
  }

  protected $attributes = ['name' => 'nombre'];

  public function updated($propertyName)
  {
    $this->validateOnly($propertyName, $this->rules(), [], $this->attributes);
  }

  public function render()
  {
    $roles = Role::orderBy('id')->get();
    return view('livewire.admin.role-component', compact('roles'));
  }

  protected function resetFields()
  {
    $this->reset('name', 'view', 'roleId');
  }

  public function store()
  {
    $this->validate($this->rules(), [], $this->attributes);
    Role::create([
      'name' => $this->name,
    ]);
    $this->emit('roleStored', $this->name);
    $this->resetFields();
  }

  protected function findRole($id)
  {
    $role = Role::find($id, ['id', 'name']);
    if($role !== null){
      return $role;
    }else{
      $message = "El recurso que se intenta eliminar no existe";
      $this->emit('roleNotFound', $message);
    }

    return null;
  }

  public function destroy($id)
  {
    $role = $this->findRole($id);
    if($role){
      $role->delete();
      $this->emit('roleDeleted', $role->name);
      $this->resetFields();
    }
  }

  public function edit($id)
  {
    $role = $this->findRole($id);
    if($role){
      $this->roleId = $role->id;
      $this->name = $role->name;
      $this->view = "edit";
    }else{
      $this->resetFields();
    }
  }

  public function update()
  {
    $this->validate($this->rules(), [], $this->attributes);
    $role = $this->findRole($this->roleId);
    if($role)
    {
      $role->update([
        'name' => $this->name
      ]);

      $this->emit('roleUpdated');
      $this->resetFields();
    }
  }
}
