<?php

namespace App\Http\Middleware;

use App\Mobile\Direction;
use App\Mobile\Event;
use App\Mobile\Group;
use App\Src\DirectionAccess;
use App\Src\EventAccess;
use App\Src\GroupAccess;
use Closure;

class CheckGroupAccess
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

        $group = Group::with('direction.event')->where('id', $request->event_id)->first() ?? new Group;

        $direction_access = new DirectionAccess($payload['user_data']->id, $group->direction->admins);

        $event_access = new EventAccess($payload['user_data']->id, $group->direction->event->admins, $direction_access);

        $group_access = new GroupAccess($payload['user_data']->id, $g->admins, $event_access);

        if (in_array(1, $group_access->getAccess())) {
            return $next($request);
        }

        return response()->json(["error" => "Access denied"], 401);
    }
}
