<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
  use HasFactory;
  protected $table = 'tag';
  protected $fillable = ['name', 'slug'];
  protected $guarded = ['id'];
}
