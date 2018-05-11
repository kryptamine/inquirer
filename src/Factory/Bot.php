<?php

namespace Inquirer\Factory;

use Inquirer\EntityStorage;
use Inquirer\Entity;
use Inquirer\Registry;

class Bot
{
    private $storage;

    public function __construct(EntityStorage $storage)
    {
        $this->storage = $storage;
    }

    public function create($username, $token)
    {
        $bot = new Entity\Bot($username, $token);
        $this->storage->addEntity($bot);
        return $bot;
    }

    /**
     * @return Entity\Bot[]
     * @throws \Inquirer\Exception\StorageException
     */
    public function getList()
    {
        $list = [];
        foreach ($this->storage->get() as $key => $data) {
            $list[] = new Entity\Bot($data->username, $data->token);
        }
        return $list;
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Inquirer\Exception\StorageException
     */
    public function getByUsername($username)
    {
        Registry::getInstance()->getLog()->debug("Retrieve bot by username '$username'");
        $data = $this->storage->get()->{$username};
        return new Entity\Bot($data->username, $data->token);
    }
}
