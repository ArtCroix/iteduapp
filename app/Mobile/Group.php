<?php

namespace App\Mobile;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function users()
    {
        //  return $this->belongsToMany('App\Moodle\MdlUserLocal','users_groups','group_id','user_id' )->withTimestamps();
        return $this->belongsToMany('App\User', 'users_groups', 'group_id', 'user_id')->withTimestamps();
    }

    public function directions()
    {
        //  return $this->belongsToMany('App\Moodle\MdlUserLocal','users_groups','group_id','user_id' )->withTimestamps();
        return $this->belongsToMany('App\Mobile\Direction', 'directions_groups', 'group_id', 'direction_id')->withTimestamps();
    }

    public function mdlusers()
    {
        return $this->belongsToMany('App\Moodle\MdlUser', 'users_groups', 'group_id', 'user_id')->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany('App\Mobile\Schedule', 'group_id');
    }

    public function direction()
    {
        return $this->belongsTo('App\Mobile\Direction');
    }

    public function getGroupadminAttribute()
    {
        return $this->users()->where('is_admin', 1)->get();
    }

    public function getAdminsAttribute()
    {
        return array_map('intval', explode(',', $this->group_admins));
    }
}
