<?php

namespace App\Mobile;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $guarded = [];

    public function emails()
    {
        //  return $this->belongsToMany('App\Moodle\MdlUserLocal','users_groups','group_id','user_id' )->withTimestamps();
        return $this->belongsToMany('App\Mobile\Email', 'emails_subjects', 'subject_id', 'email_id');
    }
}
