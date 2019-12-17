<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'mdl3_course';
    protected $connection= 'itedu';
    protected $guarded = [];

    public function quizes()
    {
        return $this->hasMany('App\Moodle\Quiz','course');
    }



}
