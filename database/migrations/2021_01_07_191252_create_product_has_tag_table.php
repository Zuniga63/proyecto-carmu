<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductHasTagTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('product_has_tag', function (Blueprint $table) {
      $table->foreignId('product_id')
        ->constrained('product')
        ->onDelete('cascade');
      $table->foreignId('tag_id')
        ->constrained('tag')
        ->onDelete('cascade');
      $table->primary(['product_id', 'tag_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('product_has_tag');
  }
}
