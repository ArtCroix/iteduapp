<?php

namespace App\Src;

use App\Src\AppEntityAccessDecorator;

class DirectionAccess extends AppEntityAccessDecorator
{
    public function isAdmin()
    {
        if (in_array($this->user_id, $this->admins, true)) {
            return ['direction_admin' => 1];
        } else {
            return ['direction_admin' => 0];
        }
    }

}
