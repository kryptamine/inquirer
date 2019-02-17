<?php

namespace Inquirer\Bridge;

use Inquirer\Api;
use Inquirer\Entity;

class Bot
{
    const BASE_WEBHOOK_URL = 'https://plesk-quizz-bot.herokuapp.com/webhook';

    /** @var Entity\Bot */
    private $bot;

    /** @var Api */
    private $api;

    public function __construct(Entity\Bot $bot, Api $api)
    {
        $this->bot = $bot;
        $this->api = $api;
    }

    public function register()
    {
        $this->api->registerWebHook($this->bot->getToken(), $this->getWebhookUrl($this->bot->getUsername()));
    }

    protected function getWebhookUrl($botUsername)
    {
        return static::BASE_WEBHOOK_URL . '/' . $botUsername;
    }
}
