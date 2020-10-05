<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
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

Route::get('/', function () {
  return view('welcome');
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//   return view('dashboard');
// })->name('dashboard');

//---------------------------------------------------------
//  RUTAS PARA EL PANEL DE ADMINISTRACION
//---------------------------------------------------------
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
  Route::view('/dashboard', 'dashboard')->name('dashboard');

  Route::name('admin.')->prefix('admin')->group(function () {
    Route::redirect('/', 'admin/dashboard', 301)->name('admin');
    Route::view('dashboard', 'admin.dashboard.index')->name('dashboard');
    // ---------------------------------------------------
    // Rutas para la gestion de los menus
    // ---------------------------------------------------
    Route::get('menu', [MenuController::class, 'index'])->name('menu');
    Route::post('menu', [MenuController::class, 'store'])->name('menu_store');
    Route::get('menu/crear', [MenuController::class, 'create'])->name(('menu_create'));
    Route::post('menu/guardar-orden', [MenuController::class, 'saveOrder']);
    // ---------------------------------------------------
    // Rutas para la gestion de los roles
    // ---------------------------------------------------
    Route::get('rol', [RoleController::class, 'index'])->name('role');
    Route::get('rol/crear', [RoleController::class, 'create'])->name('create_role');
    Route::get('rol/{id}/editar', [RoleController::class, 'edit'])->name('edit_role');
    Route::post('rol', [RoleController::class, 'store'])->name('store_role');
    Route::put('rol/{id}', [RoleController::class, 'update'])->name('update_role');
    Route::delete('rol/{id}', [RoleController::class, 'destroy'])->name('delete_role');
  });
});
