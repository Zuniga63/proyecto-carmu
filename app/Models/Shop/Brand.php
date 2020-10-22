<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
  use HasFactory;
  protected $table = 'brand';
  protected $fillable = ['name', 'slug'];
  protected $guarded = ['id'];
}
