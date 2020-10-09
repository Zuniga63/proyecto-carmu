<?php

namespace App\Providers;

use App\Models\Admin\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    /**
     * Con esto le estpi enviando a todas las vistas el
     * nombre del tema que deben utilizar
     */
    View::share('theme', 'lte');

    View::composer("theme.lte.sidebar", function($view){
      $menus = Menu::getMenus(true);
      $view->with('menusComposer', $menus);
    });
  }
}
