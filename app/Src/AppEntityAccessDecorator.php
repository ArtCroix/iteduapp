<?php

namespace App\Src;

abstract class AppEntityAccessDecorator
{

    protected $entity;
    protected $user_id;
    protected $admins;

    public function __construct($user_id, $admins, $entity = null)
    {
        if (!$entity) {
            $this->entity = new class

            {
                public function getAccess()
                {
                    return [];
                }
            };

        } else {
            $this->entity = $entity;
        }

        $this->user_id = $user_id;
        $this->admins = $admins;
    }

    public function getAccess()
    {
        return $this->accesses = array_merge($this->isAdmin(), $this->entity->getAccess());
    }

}
