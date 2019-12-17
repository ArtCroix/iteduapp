<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $table = 'mdl3_apply_value';
    protected $connection = 'itedu';
    // protected $with = ['item'];

    public function item()
    {
        return $this->belongsTo('App\Moodle\Item');
    }

    public function submit()
    {
        return $this->belongsTo('App\Moodle\Submit', 'submit_id');
    }
}
