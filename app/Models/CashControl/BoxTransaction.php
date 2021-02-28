<?php

namespace App\Models\CashControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxTransaction extends Model
{
  use HasFactory;
  protected $table = 'box_transaction';
  protected $fillable = ['transaction_date', 'description', 'type', 'amount'];
  protected $guarded = ['id'];
}
