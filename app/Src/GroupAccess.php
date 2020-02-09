<?php

namespace App\Src;

use App\Src\AppEntityAccessDecorator;

class GroupAccess extends AppEntityAccessDecorator
{
    public function isAdmin()
    {
        if (in_array($this->user_id, $this->admins, true)) {
            return ['group_admin' => 1];
        } else {
            return ['group_admin' => 0];
        }
    }
}
