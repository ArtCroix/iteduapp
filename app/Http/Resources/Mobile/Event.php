<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Mobile\DirectionCollection;


class Event extends JsonResource
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
            'event_name' => $this->event_name,
            'directions' => $this->when(true, function () {
                return new DirectionCollection($this->whenLoaded('directions'));
            }),

        ];
    }
}
