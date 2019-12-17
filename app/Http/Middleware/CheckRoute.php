<?php

namespace App\Http\Middleware;

use Closure;

class CheckRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$routes_white_list)
    {
      $routes_white_list = array_map('strtoupper', $routes_white_list);
     
      if(!in_array($request->getMethod(), $routes_white_list)){
			   return response('The method specified in the request is not allowed', 405)
                  ->header('Content-Type', 'text/plain');
		} 
		
		return $next($request);
    }
}
