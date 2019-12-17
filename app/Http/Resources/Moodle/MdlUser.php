<?php

namespace App\Http\Resources\Moodle;

use Illuminate\Http\Resources\Json\JsonResource;

/* use App\Http\Resources\Application as ApplicationResource;

use App\Http\Resources\ApplicationId as ApplicationIdResource; */

use App\Http\Resources\ApplicationCollection;

class MdlUser extends JsonResource
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
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'thirdname' => $this->thirdname,
            'username' => $this->username,
            //    'mdl_role' => $this->role,
            'approle' => $this->role,
            'email' => $this->email,
            'applications' => $this->when(true, function () {
                return new ApplicationCollection($this->whenLoaded('apps'));
            }),

            /* 		'applicationsid' => $this->when(true, function(){
				return new ApplicationCollection($this->whenLoaded('appsid'));
			}),  */
        ];
    }
}
