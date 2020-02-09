<?php

namespace App\Http\Middleware;

use App\Mobile\Direction;
use App\Mobile\Event;
use App\Mobile\Group;
use App\Src\DirectionAccess;
use App\Src\EventAccess;
use App\Src\GroupAccess;
use Closure;

class CheckDirectionAccess
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

        $direction = Direction::with(['groups', 'event'])->find($request->direction_id) ?? new Direction;

        $direction_access = new EventAccess($payload['user_data']->id, $direction->event->admins);

        $direction_access = new DirectionAccess($payload['user_data']->id, $direction->admins, $direction_access);

        if (in_array(1, $direction_access->getAccess())) {
            return $next($request);
        }

        return response()->json(["error" => "Access denied"], 401);
    }
}
