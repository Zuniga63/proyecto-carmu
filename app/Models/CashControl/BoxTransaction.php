<?php

namespace App\Models\CashControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxTransaction extends Model
{
  use HasFactory;
  protected $table = 'box_transaction';
  protected $fillable = ['box_id','transaction_date', 'description', 'type', 'amount'];
  protected $guarded = ['id'];

  public function box()
  {
    return $this->belongsTo(Box::class, 'box_id');
  }
}
