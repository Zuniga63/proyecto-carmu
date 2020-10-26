<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  protected $table = 'product';
  protected $fillable = ['brand_id', 'name', 'slug', 'img', 'description', 'price', 'stock', 'outstanding', 'published', 'is_new'];
  protected $guarded = ['id'];
}
