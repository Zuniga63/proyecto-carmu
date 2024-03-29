<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleHasMenuController;
use App\Http\Controllers\HomeController;
use App\Http\Livewire\Admin\Carmu\CustomerProfileComponent;
use App\Http\Livewire\Admin\Carmu\CustomersComponent;
use App\Http\Livewire\Admin\Carmu\SalesComponent;
use App\Http\Livewire\Admin\DashboardComponent;
use App\Http\Livewire\Admin\Shop\ColorComponent;
use App\Http\Livewire\Admin\Shop\ProductComponent;
use App\Http\Livewire\Admin\Shop\SizeComponent;
use App\Http\Livewire\CashControl\BoxConsultComponent;
use App\Http\Livewire\CashControl\ShowBoxs;
use App\Http\Livewire\SonDeCuatro\Dashboard;
use App\Http\Livewire\SonDeCuatro\ProductsComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/catalogo/{categorySlug?}', [HomeController::class, 'catalog'])->name('catalog');

Route::redirect('/register', '/login', 301);

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//   return view('dashboard');
// })->name('dashboard');

//---------------------------------------------------------
//  RUTAS PARA EL PANEL DE ADMINISTRACION
//---------------------------------------------------------
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
  Route::view('/dashboard', 'dashboard')->name('dashboard');
  Route::get('/son-de-cuatro', Dashboard::class);
  // ---------------------------------------------------
  // Rutas para la adminstracion
  // ---------------------------------------------------

  Route::name('admin.')->prefix('admin')->group(function () {
    Route::redirect('/', 'admin/dashboard', 301)->name('admin');
    Route::get('dashboard', DashboardComponent::class)->name('dashboard');
    Route::middleware(['superadmin'])->group(function () {
      // ---------------------------------------------------
      // Rutas para la gestion de los menus
      // ---------------------------------------------------
      Route::view('menu', 'admin.menu.index')->name('menu');
      // Route::get('menu', [MenuController::class, 'index'])->name('menu');
      // Route::get('menu/crear', [MenuController::class, 'create'])->name('menu_create');
      // Route::get('menu/{id}/editar', [MenuController::class, 'edit'])->name('edit_menu');
      // Route::post('menu', [MenuController::class, 'store'])->name('menu_store');
      // Route::post('menu/guardar-orden', [MenuController::class, 'saveOrder']);
      // Route::put('menu/{id}', [MenuController::class, 'update'])->name('update_menu');
      // Route::delete('menu/{id}', [MenuController::class, 'destroy'])->name('delete_menu');
      // ---------------------------------------------------
      // Rutas para la gestion de los roles
      // ---------------------------------------------------
      Route::view('rol', "admin.role.index")->name('role');
      // Route::get('rol/crear', [RoleController::class, 'create'])->name('create_role');
      // Route::get('rol/{id}/editar', [RoleController::class, 'edit'])->name('edit_role');
      // Route::post('rol', [RoleController::class, 'store'])->name('store_role');
      // Route::put('rol/{id}', [RoleController::class, 'update'])->name('update_role');
      // Route::delete('rol/{id}', [RoleController::class, 'destroy'])->name('delete_role');
      // ---------------------------------------------------
      // Rutas para la asignacion de menus
      // ---------------------------------------------------
      Route::get('asignar-menu', [RoleHasMenuController::class, 'index'])->name('menu_role');
      Route::post('menu-rol', [RoleHasMenuController::class, 'store'])->name('store_menu_rol');
      // ---------------------------------------------------
      // Rutas para la administracion de permisos
      // ---------------------------------------------------
      Route::view('permiso', 'admin.permission.index')->name('permission');
      // ---------------------------------------------------
      // Rutas para la administracion de ususarios
      // ---------------------------------------------------
      Route::view('usuarios', 'admin.users.index')->name('users');
    });

    //-----------------------------------------------------------
    // RUTAS PARA LA ADMINISTRACION DE LA TIENDA
    //-----------------------------------------------------------
    Route::view('tienda/categorias', 'admin.shop.category.index')->name('shop_categories');
    Route::view('tienda/etiquetas', 'admin.shop.tag.index')->name('shop_tags');
    Route::view('tienda/marcas', 'admin.shop.brand.index')->name('shop_brands');
    // Route::view('tienda/tallas', 'admin.shop.size.index')->name('shop_size');
    Route::get('tienda/tallas', SizeComponent::class)->name('shop_size');
    // Route::view('tienda/colores', 'admin.shop.colors.index')->name('shop_colors');
    Route::get('tienda/colores', ColorComponent::class)->name('shop_colors');
    // Route::view('tienda/productos', 'admin.shop.product.index')->name('shop_products');
    Route::get('tienda/productos', ProductComponent::class)->name('shop_products');
    //-----------------------------------------------------------
    // RUTAS PARA EL MANEJO DE LOS DATOS DE CARMÚ
    //-----------------------------------------------------------
    Route::view('carmu', 'admin.carmu.index')->name('carmu');
    Route::get('carmu/clientes/{id?}', CustomersComponent::class)->name('carmu_customers')->where('id', '[0-9]+');
    Route::get('carmu/clientes/perfiles/{id?}', CustomerProfileComponent::class)->name('carmu_profile');
    Route::get('carmu/ventas', SalesComponent::class)->name('carmu_sales');
    //-----------------------------------------------------------
    //  ADMINISTRACIÓN DE CAJAS
    //-----------------------------------------------------------
    Route::get('cajas-actuales', ShowBoxs::class)->name('showBox')->where('id', '[0-9]+');
    Route::get('cajas/consultas', BoxConsultComponent::class)->name('boxConsult');

    //----------------------------------------------------
    // ADMINISTRACION DE PRODUCTOS SONDE CUATRO
    //----------------------------------------------------
    Route::get('son-de-cuatro/productos', ProductsComponent::class)->name('sondecuatro');
  });
});
