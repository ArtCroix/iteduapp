<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;


class QuizAttempt extends JsonResource
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
            //    'id' => $this->id,
            'state' => $this->state,
        ];
    }
}
