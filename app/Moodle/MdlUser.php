<?php

namespace App\Moodle;

use App\Moodle\MdlUserLocal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class MdlUser extends Authenticatable implements JWTSubject
{
	protected $table = 'mdl3_user';
	protected $connection = 'itedu';

	//	protected $visible = ['firstname', 'lastname','email','id', 'username'];

	public function getThirdnameAttribute()
	{
		return $this->infoDatas()->where('fieldid', 4)->first()->data ?? '';
	}

	public function getRoleAttribute($value)
	{
		$admins_id = explode(',', MdlConfig::where('name', 'siteadmins')->first()->value);

		return  in_array($this->id, $admins_id) ? 'admin' : 'user';
	}

	public function getApproleAttribute($value)
	{
		return MdlUserLocal::where('id', $this->id)->first()->approle ?? 'user';
	}

	public function submits()
	{
		return $this->hasMany('App\Moodle\Submit', 'user_id');
	}

	public function groups()
	{
		return $this->belongsToMany('App\Mobile\Group', 'users_groups', 'user_id', 'group_id');
	}

	public function infoDatas()
	{
		return $this->hasMany('App\Moodle\InfoData', 'userid');
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
