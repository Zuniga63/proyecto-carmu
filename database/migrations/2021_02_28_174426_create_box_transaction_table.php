<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxTransactionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('box_transaction', function (Blueprint $table) {
      $table->id();
      $table->foreignId('box_id')->constrained('box');
      $table->dateTime('transaction_date')->useCurrent();
      $table->string('description');
      $table->enum('type', ['general', 'sale', 'expense', 'purchase', 'service', 'credit', 'payment', 'transfer'])->default('general');
      $table->decimal('amount', 10, 2);
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
    Schema::dropIfExists('box_transaction');
  }
}
