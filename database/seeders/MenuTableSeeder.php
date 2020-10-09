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
      ["name" => "Admin", "url" => "#", "icon" => "fas fa-user-cog"],
      ["name" => "Menus", "url" => "#", "icon" => "fas fa-bars"],
      ["name" => "Listado actual", "url" => "admin/menu", "icon" => "fas fa-server"],
      ["name" => "Crear Menú", "url" => "admin/menu/crear", "icon" => "fas fa-edit"],
      ["name" => "Permisos", "url" => "#", "icon" => "fas fa-cog"],
      ["name" => "Listado de permisos", "url" => "#", "icon" => "fas fa-book"],
      ["name" => "Crear permiso", "url" => "#", "icon" => "fas fa-edit"],
      ["name" => "Roles", "url" => "#", "icon" => "fas fa-user-tie"],
      ["name" => "Roles actuales", "url" => "admin/rol", "icon" => "fas fa-book"],
      ["name" => "Nuevo rol", "url" => "admin/menu/rol/crear", "icon" => "fas fa-edit"],
      ["name" => "Asignar Menús", "url" => "admin/menu-rol", "icon" => "fas fa-edit"],
      ["name" => "Asignar permisos", "url" => "#", "icon" => "fas fa-cogs"],
      ["name" => "Usuarios", "url" => "#", "icon" => "fas fa-users"],
      ["name" => "Listado", "url" => "#", "icon" => "fas fa-book"],
      ["name" => "Asignar roles", "url" => "#", "icon" => "fas fa-users-cog"],

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
