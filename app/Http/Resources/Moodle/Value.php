<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Moodle\ItemCollection;

class Value extends JsonResource
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
            'value' => $this->value,
            // 'submit_id' => $this->submit_id,
            // 'item' => $this->item->name

        ];
    }
}
