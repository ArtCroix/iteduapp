<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class Submit extends Model
{
    protected $table = 'mdl3_apply_submit';
    protected $connection = 'itedu';
    //protected $visible = ['user_id'];

    public function app()
    {
        return $this->belongsTo('App\Moodle\Application', 'apply_id');
    }

    public function values()
    {
        return $this->hasMany('App\Moodle\Value', 'submit_id');
    }

    public function items()
    {
        return $this->hasMany('App\Moodle\Item', 'apply_id', 'apply_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Moodle\MdlUser', 'user_id');
    }
}
