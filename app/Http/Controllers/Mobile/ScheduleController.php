<?php

namespace App\Http\Controllers\Mobile;

use App\Mobile\Group;

use App\Mobile\Schedule;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Mobile\GroupController;

use Illuminate\Database\QueryException;

use App\Http\Resources\Mobile\Schedule as ScheduleResource;

class ScheduleController extends Controller
{

   public function __construct()
   {
      $this->middleware('jwt.verify');
   }

   public function addSchedule($group_id)
   {
      $group = Group::find($group_id);

      if ($group) {

         GroupController::isAllowed($group);
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
            return ['error' => $ex->getMessage()];
         }
      }
      return ['error' => 'group wasn\'t found'];
   }

   public function updateSchedule($schedule_id)
   {

      $schedule = Schedule::find($schedule_id);
      $updated = request()->all();
      if ($schedule) {

         $group = Group::find($schedule->group_id);
         GroupController::isAllowed($group);
         try {
            $schedule->update($updated);
         } catch (QueryException $ex) {
            return ['error' => $ex->getMessage()];
         }
         return  ['succes' => 'schedule was updated'];
      }

      return ['error' => 'schedule wasn\'t found'];
   }

   public function addRoomInSchedule($schedule_id)
   {
      $schedule = Schedule::find($schedule_id);

      if ($schedule) {

         $group = Group::find($schedule->group_id);

         GroupController::isAllowed($group);

         try {
            $schedule->rooms()->syncWithoutDetaching(request()->room_id);
         } catch (QueryException $ex) {

            return ['error' => $ex->getMessage()];
         }
      } else {
         return ["error" => "Schedule wasn\'t found"];
      }
      return ["success" => "Room was added in schedule"];
   }

   public function deleteSchedule($schedule_id)
   {
      $schedule = Schedule::find($schedule_id);

      if ($schedule) {
         $group = Group::find($schedule->group_id);

         GroupController::isAllowed($group);

         try {
            Schedule::destroy($schedule_id);
         } catch (QueryException $ex) {
            return ['error' => $ex->getMessage()];
         }
         return ['success' => 'schedule was deleted'];
      }
      return ['error' => 'schedule wasn\'t found'];
   }

   public function getAllSchedules()
   {
      return ScheduleResource::collection(Schedule::all());
   }
}
