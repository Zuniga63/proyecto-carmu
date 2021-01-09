<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeparateProductTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('separate_product', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')
        ->constrained('product');
      $table->foreignId('customer_id')
        ->constrained('customer');
      $table->dateTime('separate_date')->useCurrent();
      $table->dateTime('expired_date');
      $table->unsignedTinyInteger('quantity');
      $table->decimal('unit_value', 19, 2);
      $table->decimal('partial_amount', 19,2);
      $table->decimal('discount', 19, 2)->nullable();
      $table->decimal('amount', 19,2);
      $table->decimal('balance', 19,2);
      $table->boolean('canceled')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('separate_product');
  }
}
