<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\RoomCollection;
class Schedule extends JsonResource
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
            'title' => $this->title,
            /*             'start' => date('d.m.Y H:i:s', $this->start),
            'end' => date('d.m.Y H:i:s', $this->end), */
            'start' =>  $this->start,
            'end' =>  $this->end,
            'group_id' => $this->group->id,
            'comment' => $this->comment,
            'room' => $this->room->room ?? null,
            'building' => $this->room->building ?? null,
            'lat' => $this->room->lat ?? null,
            'lng' => $this->room->lng ?? null,
        ];
    }
}
