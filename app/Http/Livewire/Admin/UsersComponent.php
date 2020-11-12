<?php

namespace App\Http\Livewire\Admin;

use App\Models\Admin\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class UsersComponent extends Component
{
  public $name = "";
  public $email = "";
  public $password = "";
  public $password_confirmation = "";
  public $rolId = 0;

  protected $rules = [
    'name' => 'required|max:255',
    'email' => 'required|string|email|max:255|unique:user,email',
    'password_confirmation' => 'required|string|min:8',
    'password' => 'required|string|min:8|confirmed',
  ];

  protected $attributes = [
    'name' => 'nombre',
    'email' => 'Correo',
    'password' => 'Contraseña',
    'password_confirmation' => 'Confirmación de contraseña'
  ];

  public function render()
  {
    $users = User::orderBy('id')->with('roles')->get(['id', 'name', 'email', 'created_at', 'updated_at']);
    $roles = Role::orderBy('name')->pluck('name', 'id');
    return view('livewire.admin.users-component', compact('users', 'roles'));
  }

  public function formatDate($date)
  {
    return Carbon::parse($date)->format('d-m-Y');
  }

  public function updatedName($value)
  {
    $this->name = trim($value);
  }

  public function store()
  {
    $this->name = trim($this->name);
    $this->email = trim($this->email);
    $this->password = trim($this->password);
    $this->password_confirmation = trim($this->password_confirmation);
    $this->rolId = intval($this->rolId);

    $this->validate($this->rules, [], $this->attributes);


    $user = User::create([
      'name' => $this->name,
      'email' => $this->email,
      'password' => Hash::make($this->password)
    ]);

    if($user && $this->rolId > 0){
      $user->roles()->attach($this->rolId);
    }

    $this->resetFields();
    
  }

  public function resetFields()
  {
    $this->reset('name', 'email', 'password', 'password_confirmation', 'rolId');
  }

  public function destroy($userId)
  {
    $user = User::find($userId, ['id', 'profile_photo_path']);
    if($user){
      if($user->profile_photo_path){
        $exist = Storage::disk('public')->exists($user->profile_photo_path);
        if($exist){
          Storage::disk('public')->delete($user->profile_photo_path);
        }
      }
      $user->delete();
    }
  }
}
