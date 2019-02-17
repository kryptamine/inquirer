<?php

namespace Inquirer\Service;

use Inquirer\Api;
use Inquirer\Entity\Bot;
use Inquirer\Bridge\Bot as Bridge;
use Inquirer\Exception\Exception;

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
     * BotService constructor.
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @param string $userName
     * @param string $token
     * @throws Exception
     */
    public function register(string $userName, string $token)
    {
        $bot = new Bot($userName, $token);

        $bridge = new Bridge(
            new Bot($userName, $token),
            $this->api
        );

        try {
            $bridge->register();
        } catch (\Exception $e) {
            throw new Exception("Unable to register bot '{$bot->getUsername()}': {$e->getMessage()}");
        }
    }
}