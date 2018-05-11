<?php

namespace Inquirer\Entity;

class Question implements Entity
{
    private $code;
    private $type;
    private $message;

    /** @var Option[] */
    private $options;

    /** @var Option */
    private $answer = null;

    public function __construct($code, $type, $message, $options = [], $answer = null)
    {
        $this->code = $code;
        $this->type = $type;
        $this->message = $message;
        $this->options = $options;
        $this->answer = $answer;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getKey()
    {
        return $this->code;
    }

    public function toArray()
    {
        $optionsArray = [];
        foreach ($this->options as $option) {
            $optionsArray[] = $option->toArray();
        }
        return [
            'code' => $this->code,
            'message' => $this->message,
            'options' => $optionsArray,
            'answer' => $this->answer,
        ];
    }
}
