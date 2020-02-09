<?php

namespace App\Http\Middleware;

use App\Mobile\Direction;
use App\Mobile\Event;
use App\Mobile\Group;
use App\Src\DirectionAccess;
use App\Src\EventAccess;
use App\Src\GroupAccess;
use Closure;

class CheckRoomAccess
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

        if ($payload['user_data']->approle == 'admin') {
            return $next($request);
        }

    }
}
