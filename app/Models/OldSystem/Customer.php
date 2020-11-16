<?php

namespace App\Models\OldSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  use HasFactory;
  protected $table = "customer";
  protected $primaryKey = "customer_id";
  public $timestamps = false;
  protected $connection = 'carmu';
  protected $fillable = ['first_name', 'last_name', 'nit', 'phone', 'email', 'good_customer'];
  protected $guarded = ['id'];

  public function credits()
  {
    return $this->hasMany(CustomerCredit::class, 'customer_id');
  }

  public function payments(){
    return $this->hasMany(CustomerPayment::class, 'customer_id');
  }
}
