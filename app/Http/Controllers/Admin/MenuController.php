<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuValidation;
use App\Models\Admin\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $menus = Menu::getMenus();
    return view('admin.menu.index', compact('menus'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.menu.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(MenuValidation $request)
  {
    //Se crea el menu usando eloquent y los campos fillable
    Menu::create($request->all());
    return redirect(route('admin.menu_create'))->with('message', 'Menu creado con exito');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Admin\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function show(Menu $menu)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Admin\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $menu = Menu::findOrFail($id);
    return view('admin.menu.edit', compact('menu'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Admin\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function update(MenuValidation $request, $id)
  {
    Menu::findOrFail($id)->update($request->all());
    return redirect(route('admin.menu'))->with('message', "Menú actualizado correctamente");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Admin\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function destroy(Menu $menu)
  {
    //
  }

  public function saveOrder(Request $request)
  {
    if ($request->ajax()) {
      $menu = new Menu();
      // $response = json_decode($request->menu);
      $menu->saveOrder($request->menu);
      return response()->json(['res' => 'ok']);
    } else {
      abort(484);
    }
  }
}
