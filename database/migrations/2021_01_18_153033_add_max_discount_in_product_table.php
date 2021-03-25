<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxDiscountInProductTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('product', function (Blueprint $table) {
      $table->unsignedDecimal('max_discount', 8, 2)
        ->default(0)
        ->after('price');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('product', function (Blueprint $table) {
      $table->dropColumn('max_discount');
    });
  }
}
