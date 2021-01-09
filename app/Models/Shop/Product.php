<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  protected $table = 'product';
  protected $fillable = ['brand_id', 'color_id', 'size_id', 'name', 'slug', 'img', 'ref', 'barcode', 'description', 'price', 'stock', 'outstanding', 'published', 'is_new'];
  protected $guarded = ['id'];

  // public function tags()
  // {
  //   return $this->belongsToMany(Tag::class, '')
  // }

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'category_has_product');
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class, 'product_has_tag');
  }
}
