<?php

namespace Inquirer\Bridge;

use Inquirer\Api;
use Inquirer\EntityStorage;
use Inquirer\Entity;

class Bot
{
    const BASE_WEBHOOK_URL = 'https://agxmeister.de/webhook';

    private $storage;
    private $api;

    public function __construct(EntityStorage $storage, Api $api)
    {
        $this->storage = $storage;
        $this->api = $api;
    }

    /**
     * @param Entity\Bot $bot
     * @throws \Inquirer\Exception\StorageException
     */
    public function register(Entity\Bot $bot)
    {
        $this->api->registerWebHook($bot->getToken(), $this->getWebhookUrl($bot->getUsername()));
        $this->storage->addEntity($bot);
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
        $data = $this->storage->get()->$username;
        return new Entity\Bot($data->username, $data->token);
    }

    protected function getWebhookUrl($botUsername)
    {
        return static::BASE_WEBHOOK_URL . '/' . $botUsername;
    }
}
