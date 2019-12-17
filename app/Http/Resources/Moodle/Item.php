<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Moodle\ItemCollection;

class Item extends JsonResource
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
            'app_id' => $this->apply_id,
            'question' => $this->name,
            'presentation' => $this->presentation,
            'typ' => $this->typ,
            'values' => new ValueCollection($this->values),
            // 'value' => $this->values->value,
        ];
    }
}
