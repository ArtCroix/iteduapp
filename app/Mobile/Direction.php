<?php

namespace App\Mobile;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    
    protected $guarded = [];
    
    public function event()
    {
        return $this->belongsTo('App\Mobile\Event');
    }

    public function groups()
    {
        return $this->hasMany('App\Mobile\Group');
    }

}
