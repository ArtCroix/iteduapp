<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mobile\Event;
use App\Http\Resources\Mobile\Event as EventResource;
use JWTAuth;


class EventController extends Controller
{

  public function __construct()
  {
    $this->middleware('jwt.verify');
  }

  public static function isAllowed(?Event $event)
  {
    $payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());


    if ($payload['user_data']->approle == 'admin'  || in_array($payload['user_data']->id, array_map('intval', explode(',', $event->event_admins)))) {

      return true;
    }

    exit(json_encode(["error" => "not allowed"]));
  }


  public function addEvent()
  {

    self::isAllowed(null);

    try {
      $event = Event::create([
        'event_name' => request()->event_name,
      ]);
    } catch (QueryException $ex) {
      return ['error' => $ex->getMessage()];
    }
    return ['success' => $event->id];
  }

  public function getEvent($event_id = 0)
  {
    $event = Event::with('directions')->find($event_id);

    if ($event) {

      return new EventResource($event);
    } else {
      return ['error' => 'event wasn\'t found'];
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
      self::isAllowed($event);


      $updated = request()->all();
      try {
        $event->update($updated);
      } catch (QueryException $ex) {
        return ['error' => $ex->getMessage()];
      }
      return  ['succes' => 'event was updated'];
    }

    return ['error' => 'event wasn\'t found'];
  }
}
