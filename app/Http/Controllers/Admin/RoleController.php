<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleValidation;
use App\Models\Admin\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $roles = Role::orderBY('id')->get();
    return view('admin.role.index', compact('roles'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.role.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(RoleValidation $request)
  {
    Role::create($request->all());
    return redirect(route('admin.role'))->with('message', '¡Rol creado con éxito!');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Admin\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function show(Role $role)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Admin\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $data = Role::findOrFail($id);
    return view('admin.role.edit', compact('data'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Admin\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function update(RoleValidation $request, $id)
  {
    Role::findOrFail($id)->update($request->all());
    return redirect(route('admin.role'))->with('message', '¡Rol actualizado con exito');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Admin\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    if ($request->ajax()) {
      if(Role::destroy($id)){
        return response()->json(['message' => "ok"]);
      }else{
        return response()->json(['message' => "ng"]);
      }
    }else{
      abort(404);
    }
  }
}
