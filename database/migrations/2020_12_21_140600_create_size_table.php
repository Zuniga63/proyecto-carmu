<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizeTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('size', function (Blueprint $table) {
      $table->id();
      $table->string('value', 5)->unique();
      $table->timestamps();
      $table->charset = 'utf8mb4';
      $table->collation = 'utf8mb4_spanish_ci';
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('size');
  }
}
