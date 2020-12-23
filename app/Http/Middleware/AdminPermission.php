<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminPermission
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if($this->Permission()){
      return $next($request);
    }

    return redirect(route('admin.dashboard'));
  }

  private function Permission()
  {
    if(!session()->get('role_name')){
      $userId = auth()->user()->id;
      $role_name = User::find($userId)->roles()->orderBy('id')->first()->name;
      Session::put('role_name', $role_name);
    }

    return session()->get('role_name')=== 'Administrador';
  }
}
