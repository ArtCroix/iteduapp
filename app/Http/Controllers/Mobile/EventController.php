<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Event as EventResource;
use App\Mobile\Direction;
use App\Mobile\Event;
use App\Mobile\Group;
use App\Src\DirectionAccess;
use App\Src\EventAccess;
use App\Src\GroupAccess;
use Illuminate\Http\Request;
use JWTAuth;

/**
 * Контроллер управления сущностью "Event"
 * Event является сущностью высшего порядка, куда входят сущности Direction и Group
 */

class EventController extends Controller
{

    public function addEvent()
    {

        try {
            $event = Event::create([
                'event_name' => request()->event_name,
            ]);
        } catch (QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return response()->json(['success' => $event->id], 200);
    }

    public function getEvent($event_id = 0)
    {
        $event = Event::with('directions')->find($event_id);

        if ($event) {

            return new EventResource($event);
        } else {
            return response()->json(['error' => 'event wasn\'t found'], 404);
        }
    }

    public function getAllEvents()
    {
        return EventResource::collection(Event::all());
    }

    public function updateEvent($event_id)
    {
        $event = Event::find($event_id);

        if ($event) {
            $updated = request()->all();
            try {
                $event->update($updated);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['succes' => 'event was updated'], 200);
        }

        return response()->json(['error' => 'event wasn\'t found'], 404);
    }
}
