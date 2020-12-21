<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeColumnInProduct extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('product', function (Blueprint $table) {
      $table->foreignId('size_id')
        ->nullable()
        ->after('color_id')
        ->constrained('size')
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
      $table->dropForeign(['size_id']);
      $table->dropColumn(['size_id']);
    });
  }
}
