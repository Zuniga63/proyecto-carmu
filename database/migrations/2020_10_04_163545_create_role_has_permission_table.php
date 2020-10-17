<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleHasPermissionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('role_has_permission', function (Blueprint $table) {
      $table->foreignId('role_id')->constrained('role');
      $table->foreignId('permission_id')->constrained('permission');
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
    Schema::dropIfExists('role_has_permission');
  }
}
