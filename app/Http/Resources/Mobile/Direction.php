<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Mobile\GroupCollection;


class Direction extends JsonResource
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
            'direction_name' => $this->direction_name,
            'groups' => new GroupCollection($this->groups),
        ];
    }
}
