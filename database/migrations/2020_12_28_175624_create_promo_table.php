<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('promo', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50);
      $table->string('description', 200);
      $table->dateTime('since')->useCurrent();
      $table->dateTime('until');
      $table->unsignedTinyInteger('min_item')->default(1);
      $table->unsignedTinyInteger('max_item')->nullable();
      $table->float('off', 8, 2, true);
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
    Schema::dropIfExists('promo');
  }
}
