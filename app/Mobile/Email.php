<?php

namespace App\Mobile;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public function subjects()
    {
        //  return $this->belongsToMany('App\Moodle\MdlUserLocal','users_groups','group_id','user_id' )->withTimestamps();
        return $this->belongsToMany('App\Mobile\Subject', 'emails_subjects', 'email_id', 'subject_id');
    }
}
