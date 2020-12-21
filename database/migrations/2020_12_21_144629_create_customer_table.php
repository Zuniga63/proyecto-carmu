<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customer', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50);
      $table->string('nit', 50)->nullable()->unique();
      $table->string('email', 100)->nullable()->unique();
      $table->string('phone', 20)->nullable();
      $table->string('photo')->nullable();
      $table->decimal('positive_balance', 19, 2)->nullable();
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
    Schema::dropIfExists('customer');
  }
}
