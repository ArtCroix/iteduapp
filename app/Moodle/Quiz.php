<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'mdl3_quiz';
    protected $connection= 'itedu';
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo('App\Moodle\Course','course');
    }

    public function attempts()
    {
        return $this->belongsTo('App\Moodle\QuizAttempt','quiz');
    }
}
