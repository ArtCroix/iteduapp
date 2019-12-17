<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\QuizCollection;


class Course extends JsonResource
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
            'shortname' => $this->shortname,
            'quizes' => $this->when(true, function () {
                return new QuizCollection($this->whenLoaded('quizes'));
            }),
            //  'quizes' => new QuizCollection($this->quizes),

        ];
    }
}
