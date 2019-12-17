<?php

namespace App\Moodle;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    
    protected $table = 'mdl3_quiz_attempts';
    protected $connection= 'itedu';
    protected $guarded = [];

}
