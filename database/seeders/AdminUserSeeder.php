<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    /**
     * Se crea los datos del administrador
     * por defecto
     */
    DB::table('user')->insert([
      'id' => 1,
      'name' => "Andrés Zuñiga",
      'email' => "andres.zuniga.063@gmail.com",
      'password' => Hash::make('clave1234'),
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    /**
     * Se asigna el rol de administrador
     */
    DB::table('user_has_role')->insert([
      'user_id' => 1,
      'role_id' => 1,
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    DB::table('user')->insert([
      'id' => 2,
      'name' => "Editor",
      'email' => "pipe6393@gmail.com",
      'password' => Hash::make('clave1234'),
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    DB::table('user_has_role')->insert([
      'user_id' => 2,
      'role_id' => 2,
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
  }
}
