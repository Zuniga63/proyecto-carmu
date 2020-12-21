<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeparatePaymentTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('separate_payment', function (Blueprint $table) {
      $table->id();
      $table->foreignId('separate_product_id')
        ->constrained('separate_product');
      $table->foreignId('customer_id')
        ->constrained('separate_product', 'customer_id');
      $table->foreignId('product_id')
        ->constrained('separate_product', 'product_id');
      $table->dateTime('payment_date')->useCurrent();
      $table->decimal('amount', 19, 2);
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
    Schema::dropIfExists('separate_payment');
  }
}
