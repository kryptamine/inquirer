<?php

namespace Inquirer\Entity;

class Bot implements Entity
{
    private $username;
    private $token;

    public function __construct($username, $token)
    {
        $this->username = $username;
        $this->token = $token;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getKey()
    {
        return $this->getUsername();
    }

    public function toArray()
    {
        return [
            'username' => $this->getUsername(),
            'token' => $this->getToken(),
        ];
    }
}
