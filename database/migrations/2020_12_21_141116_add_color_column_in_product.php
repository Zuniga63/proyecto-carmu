<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorColumnInProduct extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('product', function (Blueprint $table) {
      $table->foreignId('color_id')
        ->nullable()
        ->after('brand_id')
        ->constrained('color')
        ->onDelete('set null')
        ->onUpdate('cascade');
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
      $table->dropForeign(['color_id']);
      $table->dropColumn('color_id');
    });
  }
}
