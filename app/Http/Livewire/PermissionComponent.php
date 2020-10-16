<?php

namespace App\Http\Livewire;

use App\Models\Admin\Permission;
use Livewire\Component;
// use Livewire\WithPagination;

class PermissionComponent extends Component
{
  // use WithPagination;
  public $view = "create";
  public $permission_id, $name, $slug;

  public function store()
  {
    $this->slug = str_replace(' ', '-', mb_strtolower($this->name));
    
    $this->fieldValidation();

    $permission =  Permission::create([
      'name' => $this->name,
      'slug' => $this->slug,
    ]);

    $this->edit($permission->id);
  }

  protected function fieldValidation()
  {
    $this->validate([
      'name' => "required|max:50|unique:permission,name," . $this->permission_id,
      'slug' => 'required|max:50|unique:permission,slug,' . $this->permission_id,
    ], [], ['name' => 'nombre']);
  }

  public function render()
  {
    $permissions = Permission::get();
    return view('livewire.permission-component', compact('permissions'));
  }

  public function destroy($id)
  {
    Permission::destroy($id);
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

  public function default()
  {
    $this->permission_id = '';
    $this->name = '';
    $this->slug = '';
    $this->view = 'create';
  }
}
