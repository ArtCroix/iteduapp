<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Moodle\ApplicationCollection;
use App\Http\Resources\Mobile\EventCollection;
use App\Http\Resources\Mobile\GroupCollection;
use App\Http\Resources\Mobile\GroupsUsers;


class User extends JsonResource
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
            'email' => $this->email,
            'approle' => $this->approle,
            'vk_id' => $this->vk_id,
            'phone' => $this->phone,
            'groups' => GroupsUsers::collection($this->whenLoaded('groups')),
            // 'device_tokens' => $this->tokens()->get()->pluck("device_token"),

            'events' => $this->when(true, function () {
                return new EventCollection($this->whenLoaded('events'));
            }),
            'applications' => $this->when(true, function () {
                return new ApplicationCollection($this->whenLoaded('apps'));
            }),
        ];
    }
}
