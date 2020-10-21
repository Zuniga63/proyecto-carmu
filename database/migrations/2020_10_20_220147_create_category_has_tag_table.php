<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryHasTagTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('category_has_tag', function (Blueprint $table) {
      $table->foreignId('category_id')->constrained('category')->onDelete('cascade');
      $table->foreignId('tag_id')->constrained('tag')->onDelete('cascade');
      $table->primary(['category_id', 'tag_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('category_has_tag');
  }
}
