<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

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
        ];
    }
}
