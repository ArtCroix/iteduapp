<?php

namespace App\Http\Resources\Mobile;

use App\Http\Resources\Mobile\ScheduleCollection;
use App\Http\Resources\Mobile\UserCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupsUsers extends JsonResource
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
            'event_name' => $this->direction->event->event_name,
            'users_count' => $this->users()->count(),
            /*             'event_id' => $this->direction->event->id,
        'direction' => $this->direction->direction_name,
        'type' => $this->type,
        'users' => new UserCollection($this->users),
        'schedules' => new ScheduleCollection($this->schedules) */
        ];
    }
}
