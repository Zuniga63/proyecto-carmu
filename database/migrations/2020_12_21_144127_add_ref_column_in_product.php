<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefColumnInProduct extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('product', function (Blueprint $table) {
      $table->string('ref', 50)
        ->nullable()
        ->after('img');
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
      $table->dropColumn('ref');
    });
  }
}
