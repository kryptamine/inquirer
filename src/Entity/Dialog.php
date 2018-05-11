<?php

namespace Inquirer\Entity;

class Dialog implements Entity
{
    private $code;
    private $name;

    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getKey()
    {
        return $this->getCode();
    }

    public function toArray()
    {
        return [];
    }
}
