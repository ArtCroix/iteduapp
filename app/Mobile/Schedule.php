<?php

namespace App\Mobile;


use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = [];

    public function rooms()
    {
        return $this->belongsToMany('App\Mobile\Room', 'rooms_schedules')->withTimestamps();
    }

    public function group()
    {
        return $this->belongsTo('App\Mobile\Group');
    }
}
