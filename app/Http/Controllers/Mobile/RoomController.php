<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Room as RoomResource;
use App\Mobile\Room;
use Illuminate\Http\Request;
use JWTAuth;

class RoomController extends Controller
{

    public function addRoom()
    {

        try {
            Room::create([
                'room' => request()->room,
            ]);
        } catch (QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return response()->json(['success' => 'room was added'], 200);
    }

    public function getRoom($room_id = 0)
    {
        $room = Room::with('directions')->find($room_id);

        if ($room) {
            return new RoomResource($room);
        } else {
            return response()->json(['error' => 'room wasn\'t found'], 404);

        }
    }

    public function updateRoom($room_id)
    {

        $room = Room::find($room_id);

        if ($room) {

            $updated = request()->all();
            try {
                $room->update($updated);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['succes' => 'room was updated'], 200);
        }

        return response()->json(['error' => 'room wasn\'t found'], 404);
    }

    public function getAllRooms()
    {
        return RoomResource::collection(Room::all());
    }
}
