<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

		//we allow getting data without authentication
		if($_SERVER['REQUEST_METHOD'] == "GET") return $next($request);

		$session = \Publications\StaffUser::currentUser();

		if (!$session['username'] || !$session['role']) {
			return response('Unauthorized.', 401);
		}

		$request->identity = (object) $session;


		return $next($request);
    }
}
