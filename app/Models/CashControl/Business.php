<?php

namespace App\Models\CashControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
  use HasFactory;
  protected $table = 'business';
  protected $fillable = ['name', 'district', 'address', 'phone'];
  protected $guarded = ['id'];

  public function boxs()
  {
    return $this->hasMany(Box::class, 'business_id');
  }
}
