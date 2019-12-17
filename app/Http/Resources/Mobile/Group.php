<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Mobile\ScheduleCollection;

use App\Http\Resources\Mobile\UserCollection;

class Group extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'group_name' => $this->group_name,

            'users_count' => $this->users()->count(),
            'event_id' => $this->direction->event->id,
            'event_name' => $this->direction->event->event_name,
            'direction' => $this->direction->direction_name,
            'type' => $this->type,
            'users' => $this->when(true, function () {
                return new UserCollection($this->whenLoaded('users'));
            }),
            'schedules' => $this->when(true, function () {
                return new ScheduleCollection($this->whenLoaded('schedules'));
            }),
            // 'schedules' => new ScheduleCollection($this->schedules)
        ];
    }
}
