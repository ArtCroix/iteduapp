<?php

namespace App\Mobile;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $guarded = [];

    public function groups()
    {
        return $this->hasMany('App\Mobile\Group');
    }

    public function directions()
    {
        return $this->hasMany('App\Mobile\Direction');
    }

    public function getAdminsAttribute()
    {
        return array_map('intval', explode(',', $this->event_admins));
    }
}
