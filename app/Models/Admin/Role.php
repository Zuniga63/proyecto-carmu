<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  use HasFactory;
  protected $table = 'role';
  protected $fillable = ['name'];
  protected $guarded = ['id'];

  public function menus()
  {
    return $this->belongsToMany(Menu::class, 'role_has_menu');
  }
}
