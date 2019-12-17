<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Mobile\EventController;

use App\Mobile\Direction;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Database\QueryException;

use App\Mobile\Event;

use App\Http\Resources\Mobile\Direction as DirectionResource;

class DirectionController extends Controller
{

  public function __construct()
  {
    $this->middleware('jwt.verify');
  }

  public static function isAllowed(Direction $direction)
  {
    $payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());

    $current_event_admins = $direction->event->event_admins ? array_map('intval', explode(',', $direction->event->event_admins)) : [];

    $current_direction_admins = $direction->direction_admins ? array_map('intval', explode(',', $direction->direction_admins)) : [];

    if ($payload['user_data']->approle == 'admin' || in_array($payload['user_data']->id, $current_event_admins) || in_array($payload['user_data']->id, $current_direction_admins)) {

      return true;
    }

    exit(json_encode(["error" => "not allowed"]));
  }

  public function addDirection()
  {

    EventController::isAllowed(Event::find(request()->event_id));

    try {

      $direction = Direction::create([
        'event_id' => request()->event_id,
        'direction_name' => request()->direction_name,
      ]);
    } catch (QueryException $ex) {
      return ['error' => $ex->getMessage()];
    }
    return ['success' => $direction->id];
  }

  public function getDirection($direction_id = 0)
  {
    $direction = Direction::with('groups')->find($direction_id);

    if ($direction) {

      return new DirectionResource($direction);
    } else {
      return ['error' => 'direction wasn\'t found'];
    }
  }



  public function getAllDirections()
  {
    $directions = Direction::all();

    return DirectionResource::collection($directions);
  }


  public function updateDirection($direction_id)
  {
    $direction = Direction::find($direction_id);

    if ($direction) {
      self::isAllowed($direction);

      if (isset(request()->direction_admins)) {

        $direction_admins = array_map('intval', explode(',', request()->direction_admins));

        $current_direction_admins = $direction->direction_admins ? array_map('intval', explode(',', $direction->direction_admins)) : [];

        request()->direction_admins = implode(",", array_diff($current_direction_admins, $direction_admins));
      }

      $updated = request()->all();
      try {

        $direction->update($updated);
      } catch (QueryException $ex) {
        return ['error' => $ex->getMessage()];
      }
      return  ['succes' => 'direction was updated'];
    }

    return ['error' => 'direction wasn\'t found'];
  }
}
