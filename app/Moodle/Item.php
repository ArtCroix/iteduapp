<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $table = 'mdl3_apply_item';
	protected $connection = 'itedu';

	public function values()
	{
		return $this->hasMany('App\Moodle\Value', 'item_id');
	}
}
