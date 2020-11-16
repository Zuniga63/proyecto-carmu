<?php

namespace App\Models\OldSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
  use HasFactory;
  protected $table = "customer_payment";
  protected $primaryKey = "customer_payment_id";
  public $timestamps = false;
  protected $connection = 'carmu';
  protected $fillable = ['customer_id', 'payment_date', 'cash', 'amount'];
  protected $guarded = ['customer_payment_id'];
}
