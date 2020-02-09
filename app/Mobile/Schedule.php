<?php

namespace App\Mobile;


use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo('App\Mobile\Room');
    }

    public function group()
    {
        return $this->belongsTo('App\Mobile\Group');
    }
}
