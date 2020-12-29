<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
  use HasFactory;
  protected $table = 'size';
  protected $fillable = ['value'];
  protected $guarded = ['id'];
}
