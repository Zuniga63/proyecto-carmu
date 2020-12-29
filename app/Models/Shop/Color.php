<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
  use HasFactory;
  protected $table = 'color';
  protected $fillable = ['name', 'hex'];
  protected $guarded = ['id'];
}
