<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $guarded = ['jwt_token'];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function groups()
    {
        return $this->belongsToMany('App\Mobile\Group', 'users_groups', 'user_id');
    }

    public function getGroupsIdAttribute()
    {
        $groups_id = [];

        foreach ($this->groups as $group) {
            array_push($groups_id, $group->id);
        }

        return $groups_id;
    }

    public function events()
    {
        return $this->belongsToMany('App\Mobile\Event', 'users_groups', 'user_id');
    }

    public function tokens()
    {
        return $this->hasMany('App\Mobile\Token', 'user_id') ?? "";
    }

    public function submits()
    {
        return $this->hasMany('App\Moodle\Submit', 'user_id');
    }

    public function apps()
    {
        return $this->belongsToMany('App\Moodle\Application', 'mdl3_apply_submit', 'user_id', 'apply_id')->withPivot('apply_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
