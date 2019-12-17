<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Moodle\ValueCollection;
use App\Http\Resources\Moodle\ItemCollection;


class Submit extends JsonResource
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
            //'values' => $this->values,
            'lastname' => $this->user->lastname,
            'firstname' => $this->user->firstname,
            // 'thirdname' => $this->user->thirdname,
            /*           'values' => $this->values->id,
            // 'items' => $this->items->name,
          
 */
            // 'items' => new ItemCollection($this->items),
            'values' => $this->values()->pluck("value"),
            /*         'groups' => $this->when(true, function(){
				return new GroupCollection($this->whenLoaded('groups'));


            //    'direction_name' => $this->direction_name,
            //  'groups' => new GroupCollection($this->groups),
            /*         'groups' => $this->when(true, function(){
				return new GroupCollection($this->whenLoaded('groups'));
			}), 	 
         /*    'groups' => $this->when(true, function(){

                $group = Group::all();
				return new GroupCollection($group);
			}),	  */
            /*      'event' => $this->event->event_name,
            'users' => $this->when(true, function(){
				return new MdlUserLocalCollection($this->whenLoaded('users'));
			}),	 */
        ];
    }
}
