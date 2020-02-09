<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckUserAccess
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
        $payload = auth('api')->payload()->toArray();

        if ($payload['user_data']->approle == 'admin' || $payload['user_data']->id == $request->user->id) {
            return $next($request);
        }

        return response()->json(["error" => "Access denied"], 401);
    }
}
