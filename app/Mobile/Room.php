<?php

namespace App\Mobile;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
   protected $guarded = [];

   public function schedules()
   {
       return $this->hasMany('App\Mobile\Schedule','room_id');
   }
}
