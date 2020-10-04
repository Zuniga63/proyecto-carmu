<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $roles = [
      'Administrador',
      'Editor',
      'Supervisor',
      'Vendedor',
    ];

    foreach($roles as $key => $role){
      DB::table('role')->insert([
        'name' => $role,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]);
    }
  }
}
