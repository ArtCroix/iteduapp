<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class InfoData extends Model
{
	protected $table = 'mdl3_user_info_data';
	protected $connection= 'itedu';
	
	public function mdlUser()
    {
        return $this->belongsTo('App\Moodle\MdlUser');
    }
}
