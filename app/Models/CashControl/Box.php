<?php

namespace App\Models\CashControl;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
  use HasFactory;
  protected $table = 'box';
  protected $fillable = ['name', 'main'];
  protected $guarded = ['id'];

  public function business()
  {
    return $this->belongsTo(Business::class, 'business_id');
  }

  public function cashier()
  {
    return $this->belongsTo(User::class, 'cashier_id');
  }

  public function transactions()
  {
    return $this->hasMany(BoxTransaction::class, 'box_id');
  }
}
