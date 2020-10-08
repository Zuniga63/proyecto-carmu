<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $menus = [
      ["name" => "Dashboard", "url" => "admin/dashboard", "icon" => "fas fa-tachometer-alt"],
      ["name" => "Menu", "url" => "admin/menu", "icon" => "fas fa-server"],
      ["name" => "Crear Menú", "url" => "admin/menu/crear", "icon" => null],
    ];

    foreach($menus as $key => $menu){
      DB::table('menu')->insert([
        'name' => $menu['name'],
        'url' => $menu['url'],
        'icon' => $menu['icon'],
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]);
    }
  }
}
