<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
  use HasFactory;
  protected $table = 'menu';
  protected $fillable = ['name', 'url', 'icon'];
  protected $guarded = ['id'];
}
