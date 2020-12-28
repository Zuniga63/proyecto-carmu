<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductHasPromoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('product_has_promo', function (Blueprint $table) {
      $table->foreignId('product_id')
        ->constrained('product')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('promo_id')
        ->constrained('promo')
        ->onDelete('cascade');
      $table->primary(['product_id', 'promo_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('product_has_promo');
  }
}
