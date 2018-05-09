<?php

namespace Inquirer\Entity;

class Chat implements Entity
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->getId();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
        ];
    }
}
