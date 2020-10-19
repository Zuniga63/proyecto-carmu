<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('category', function (Blueprint $table) {
      $table->id();
      $table->foreignId('father_id')->nullable()->constrained('category')->onDelete('set null');
      $table->string('name', 50);
      $table->string('slug', 50);
      $table->string('icon', 50)->nullable();
      $table->unsignedTinyInteger('order')->default(0);
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
    Schema::dropIfExists('category');
  }
}
