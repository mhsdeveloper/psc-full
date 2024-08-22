<?php

namespace App\Http\Middleware;

use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
      if ($permission === 'edit' && $request->identity->role === 'author') {
        return response('User does not have permission.', 401);
      }
      return $next( $request );
    }
}
