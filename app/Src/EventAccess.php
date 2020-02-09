<?php

namespace App\Src;

use App\Src\AppEntityAccessDecorator;

class EventAccess extends AppEntityAccessDecorator
{
    public function isAdmin()
    {
        if (in_array($this->user_id, $this->admins, true)) {
            return ['event_admin' => 1];
        } else {
            return ['event_admin' => 0];
        }
    }

}
