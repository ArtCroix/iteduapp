<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SubmitCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
