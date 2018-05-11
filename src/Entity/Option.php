<?php

namespace Inquirer\Entity;

class Option implements Entity
{
    private $code;
    private $message;
    private $value;

    public function __construct($code, $message, $value)
    {
        $this->code = $code;
        $this->message = $message;
        $this->value = $value;
    }

    public function getKey()
    {
        return $this->code;
    }

    public function toArray()
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'value' => $this->value,
        ];
    }
}
