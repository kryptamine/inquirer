<?php

namespace Inquirer\Service;

use Inquirer\Api;
use Inquirer\Factory\Bot;
use Inquirer\Entity\Bot as BotEntity;
use Inquirer\Bridge\Bot as Bridge;
use Inquirer\Exception\Exception;
use Inquirer\EntityStorage;

/**
 * Class BotService
 * @package Inquirer\Service
 */
class BotService
{
    /**
     * @var Api
     */
    private $api;

    /**
     * @var EntityStorage
     */
    private $storage;

    /**
     * BotService constructor.
     * @param Api $api
     * @param EntityStorage $storage
     */
    public function __construct(Api $api, EntityStorage $storage)
    {
        $this->api = $api;
        $this->storage = $storage;
    }

    /**
     * @param string $name
     * @param string $token
     * @throws Exception
     */
    public function register(string $name, string $token)
    {
        $botFactory = new Bot($this->storage);
        $bridge = new Bridge(
            new BotEntity($name, $token),
            $this->api
        );

        try {
            $bridge->register();
            $botFactory->create($name, $token);
        } catch (\Exception $e) {
            throw new Exception("Unable to register bot '{$name}': {$e->getMessage()}");
        }
    }
}