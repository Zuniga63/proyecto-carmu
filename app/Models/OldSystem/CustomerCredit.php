<?php

namespace App\Models\OldSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCredit extends Model
{
  use HasFactory;
  protected $table = "customer_credit";
  protected $primaryKey = "customer_credit_id";
  public $timestamps = false;
  protected $connection = 'carmu';
  protected $fillable = ['customer_id', 'credit_date', 'description', 'amount'];
  protected $guarded = ['customer_credit_id'];
}
