<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
    return session()->get('role_name')=== 'Administrador';
  }
}
