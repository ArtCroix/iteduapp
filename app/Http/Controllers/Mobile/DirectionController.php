<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\EventController;
use App\Http\Resources\Mobile\Direction as DirectionResource;
use App\Mobile\Direction;
use App\Mobile\Event;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Контроллер управления сущностью "Direction"
 * Direction служит для объединения нескольких групп; является промежуточным звеном между сущностями "Событие (Event)" и
 * "Группа (Group)"
 */
class DirectionController extends Controller
{

    public function addDirection()
    {
        try {

            $direction = Direction::create([
                'event_id' => request()->event_id,
                'direction_name' => request()->direction_name,
            ]);
        } catch (QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return response()->json(['success' => $direction->id], 200);
    }

    public function getDirection($direction_id = 0)
    {
        $direction = Direction::with('groups')->find($direction_id);

        if ($direction) {

            return new DirectionResource($direction);
        } else {
            return response()->json(['error' => 'direction wasn\'t found'], 404);
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

            $updated = request()->all();
            try {
                $direction->update($updated);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['succes' => 'direction was updated'], 200);
        }

        return response()->json(['error' => 'direction wasn\'t found'], 404);
    }
}
