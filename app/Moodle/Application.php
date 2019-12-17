<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
	protected $table = 'mdl3_apply';
	protected $connection = 'itedu';


	public function submits()
	{
		return $this->hasMany('App\Moodle\Submit', 'apply_id');
	}

	public function items()
	{
		return $this->hasMany('App\Moodle\Item', 'apply_id');
	}
}
