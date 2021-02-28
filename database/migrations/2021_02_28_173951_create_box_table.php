<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('box', function (Blueprint $table) {
      $table->id();
      $table->foreignId('cashier_id')->nullable()->constrained('user');
      $table->foreignId('business_id')->nullable()->constrained('business');
      $table->string('name', 50);
      $table->boolean('main')->default(0);
      $table->decimal('base', 10,2)->default(0);
      $table->dateTime('closing_date')->useCurrent();
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
    Schema::dropIfExists('box');
  }
}
