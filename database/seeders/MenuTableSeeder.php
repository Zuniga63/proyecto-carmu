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
      ["id" => 1, "fatherId" => null, "order" => 1, "name" => "Dashboard", "url" => "admin/dashboard", "icon" => "fas fa-tachometer-alt"],
      ["id" => 2, "fatherId" => null, "order" => 2, "name" => "Administración", "url" => "#", "icon" => "fas fa-user-cog"],
      ["id" => 3, "fatherId" => 2, "order" => 1, "name" => "Gestionar Menús", "url" => "admin/menu", "icon" => "fas fa-server"],
      ["id" => 4, "fatherId" => 2, "order" => 2, "name" => "Asignar Menús", "url" => "admin/asignar-menu", "icon" => "fas fa-edit"],
      ["id" => 5, "fatherId" => 2, "order" => 3, "name" => "Permisos", "url" => "admin/permiso", "icon" => "fas fa-hand-paper"],
      ["id" => 6, "fatherId" => 2, "order" => 4, "name" => "Roles", "url" => "admin/role", "icon" => "fas fa-user-tie"],
      ["id" => 7, "fatherId" => null, "order" => 3, "name" => "Usuarios", "url" => "#", "icon" => "fas fa-users"],
      ["id" => 8, "fatherId" => 7, "order" => 1, "name" => "Listado", "url" => "#", "icon" => "fas fa-book"],
      ["id" => 9, "fatherId" => 8, "order" => 2, "name" => "Asignar roles", "url" => "#", "icon" => "fas fa-users-cog"],
      ["id" => 10, "fatherId" => null, "order" => 4, "name" => "Tienda", "url" => "#", "icon" => "fas fa-store"],
      ["id" => 11, "fatherId" => 10, "order" => 1, "name" => "Categorías", "url" => "admin/tienda/categorias", "icon" => "fas fa-book"],
      ["id" => 12, "fatherId" => 10, "order" => 2, "name" => "Etiquetas", "url" => "admin/tienda/etiquetas", "icon" => "fas fa-tag"],
      ["id" => 13, "fatherId" => 10, "order" => 3, "name" => "Marcas", "url" => "admin/tienda/marcas", "icon" => "fas fa-copyright"],
      ["id" => 14, "fatherId" => 10, "order" => 4, "name" => "Productos", "url" => "admin/tienda/productos", "icon" => "fas fa-shopping-cart"],
    ];

    foreach($menus as $key => $menu){
      DB::table('menu')->insert([
        'id' => $menu['id'],
        'father_id' => $menu['fatherId'],
        'order' => $menu['order'],
        'name' => $menu['name'],
        'url' => $menu['url'],
        'icon' => $menu['icon'],
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]);
    }
  }
}
