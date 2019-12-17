<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mobile\Room;
use App\Http\Resources\Mobile\Room as RoomResource;
use JWTAuth;


class RoomController extends Controller
{

  public function __construct()
  {
    $this->middleware('jwt.verify');
  }

  public static function isAllowed()
  {
    $payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());


    if ($payload['user_data']->approle == 'admin') {

      return true;
    }

    exit(json_encode(["error" => "not allowed"]));
  }

  public function addRoom()
  {

    self::isAllowed();

    try {
      Room::create([
        'room' => request()->room,
      ]);
    } catch (QueryException $ex) {
      return ['error' => $ex->getMessage()];
    }
    return ['success' => 'room was added'];
  }

  public function getRoom($room_id = 0)
  {
    $room = Room::with('directions')->find($room_id);

    self::isAllowed();

    if ($room) {
      return new RoomResource($room);
    } else {
      return ['error' => 'room wasn\'t found'];
    }
  }

  public function updateRoom($room_id)
  {

    $room = Room::find($room_id);

    if ($room) {
      self::isAllowed();

      $updated = request()->all();
      try {
        $room->update($updated);
      } catch (QueryException $ex) {
        return ['error' => $ex->getMessage()];
      }
      return  ['succes' => 'room was updated'];
    }

    return ['error' => 'room wasn\'t found'];
  }

  public function getAllRooms()
  {
    return RoomResource::collection(Room::all());
  }
}
