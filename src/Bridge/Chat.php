<?php

namespace Inquirer\Bridge;

use Inquirer\Api;
use Inquirer\Entity;
use Inquirer\Factory;
use Inquirer\Registry;

class Chat
{
    private $chat;
    private $api;

    public function __construct(\Inquirer\Chat $chat, Api $api)
    {
        $this->chat = $chat;
        $this->api = $api;
    }

    public function keepConversation($conversationItem)
    {
        $messageId = $this->api->sendMessage(
            $this->getBot()->getToken(),
            $this->chat->getId(),
            $conversationItem->message,
            isset($conversationItem->options) ? $conversationItem->options : []
        );
        $this->chat->addMessageId($messageId);
    }

    public function removeOptions($conversationItem)
    {
        $this->api->editMessageReplyMarkup(
            $this->getBot()->getToken(),
            $this->chat->getId(),
            $conversationItem->messageId
        );
    }

    public function confirmAnswer($callbackId)
    {
        $this->api->answerCallbackQuery(
            $this->getBot()->getToken(),
            $callbackId
        );
    }

    /**
     * @return Entity\Bot
     */
    protected function getBot()
    {
        $botFactory = new Factory\Bot(Registry::getInstance()->getApp()['botStorage']);
        return $botFactory->getByUsername($this->chat->getBotUsername());
    }

    protected function getDialogStoragePath($dialogName)
    {
        $baseStoragePath = Registry::getInstance()->getApp()['baseStoragePath'];
        return $baseStoragePath . DIRECTORY_SEPARATOR . 'dialogs' . DIRECTORY_SEPARATOR . $dialogName;
    }
}
