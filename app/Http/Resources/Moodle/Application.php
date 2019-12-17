<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Moodle\SubmitCollection;

use App\Http\Resources\Moodle\ItemCollection;

use Illuminate\Http\Request;

use App\Moodle\Value;

use App\Moodle\Submit;

class Application extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $user_id = $request->route()->parameters()['user_id'] ?? '';


        return [
            'apply_id' => $this->id,
            'apply_name' => $this->name,
            /*      'items' => $this->when(true, function () {
                return new ItemCollection($this->whenLoaded('items'));
            }),

            'items' => $this->when(true, function () use ($user_id) {
                return Value::where('submit_id', Submit::where('apply_id', $this->id)->first()->id)->join('mdl3_apply_item', 'mdl3_apply_item.id', '=', 'mdl3_apply_value.item_id')->get(['item_id as question_id', 'mdl3_apply_item.name as question', 'mdl3_apply_value.id as answer_id', 'value as answer']);
            }),
*/
            'submits' => $this->when(true, function () {
                return new SubmitCollection($this->whenLoaded('submits'));
            }),


        ];
    }
}
