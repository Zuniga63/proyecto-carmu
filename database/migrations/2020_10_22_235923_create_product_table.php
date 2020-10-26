<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('product', function (Blueprint $table) {
      $table->id();
      $table->foreignId('brand_id')
        ->nullable()
        ->constrained('brand')
        ->onDelete('set null');
      $table->string('name', 50);
      $table->string('slug', 50);
      $table->string('img')->nullable();
      $table->text('description');
      $table->decimal('price', 10, 2)->default(0);
      $table->unsignedTinyInteger('stock')->default(0);
      $table->boolean('outstanding')->default(false);
      $table->boolean('is_new')->default(false);
      $table->boolean('published')->default(false);
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
    Schema::dropIfExists('product');
  }
}
