<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeColumnInProduct extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('product', function (Blueprint $table) {
      $table->string('barcode')
        ->nullable()
        ->unique()
        ->after('ref');
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
      $table->dropColumn('barcode');
    });
  }
}
