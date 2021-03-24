<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSondecuatroTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('sondecuatro', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50);
      $table->decimal('expense', 9, 2);
      $table->decimal('price', 9, 2);
      $table->string('img')->nullable();
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
    Schema::dropIfExists('sondecuatro');
  }
}
