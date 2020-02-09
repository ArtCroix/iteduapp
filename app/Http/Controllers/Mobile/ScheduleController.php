<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\GroupController;
use App\Http\Resources\Mobile\Schedule as ScheduleResource;
use App\Mobile\Group;
use App\Mobile\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function addSchedule($group_id)
    {
        $group = Group::find($group_id);

        if ($group) {

            try {
                $schedule = Schedule::create([
                    'title' => request()->title,
                    'start' => request()->start,
                    'end' => request()->end,
                    'group_id' => $group->id,
                    'comment' => request()->comment ?? '',
                ]);

                return $schedule;
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'group wasn\'t found'], 404);
    }

    public function updateSchedule($schedule_id)
    {

        $schedule = Schedule::find($schedule_id);
        $updated = request()->all();
        if ($schedule) {

            $group = Group::find($schedule->group_id);

            try {
                $schedule->update($updated);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['succes' => 'schedule was updated'], 200);
        }

        return response()->json(['error' => 'schedule wasn\'t found'], 404);
    }

    public function addRoomInSchedule($schedule_id)
    {
        $schedule = Schedule::find($schedule_id);

        if ($schedule) {

            $group = Group::find($schedule->group_id);

            try {
                $schedule->rooms()->syncWithoutDetaching(request()->room_id);
            } catch (QueryException $ex) {

                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json(["error" => "Schedule wasn\'t found"], 404);
        }
        return response()->json(["success" => "Room was added in schedule"], 200);
    }

    public function deleteSchedule($schedule_id)
    {
        $schedule = Schedule::find($schedule_id);

        if ($schedule) {
            $group = Group::find($schedule->group_id);

            try {
                Schedule::destroy($schedule_id);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['success' => 'schedule was deleted'], 200);
        }
        return response()->json(['error' => 'schedule wasn\'t found'], 404);
    }

    public function getAllSchedules()
    {
        return ScheduleResource::collection(Schedule::all());
    }
}
