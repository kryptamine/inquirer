<?php

namespace Inquirer\Bridge;

use Inquirer\Api;
use Inquirer\Registry;
use Inquirer\Entity\ConversationItem;

/**
 * Class Chat
 * @package Inquirer\Bridge
 */
class Chat
{
    private $chat;
    private $api;

    /**
     * Chat constructor.
     * @param \Inquirer\Chat $chat
     * @param Api $api
     */
    public function __construct(\Inquirer\Chat $chat, Api $api)
    {
        $this->chat = $chat;
        $this->api = $api;
    }

    /**
     * @param ConversationItem $conversationItem
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function keepConversation(ConversationItem $conversationItem)
    {
        $messageId = $this->api->sendMessage(
            $this->chat->getId(),
            $conversationItem
        );
        $this->chat->addMessageId($messageId);
    }

    /**
     * @param string $messageId
     */
    public function removeOptions(string $messageId)
    {
        $this->api->editMessageReplyMarkup(
            $this->chat->getId(),
            $messageId
        );
    }

    public function confirmAnswer($callbackId)
    {
        $this->api->answerCallbackQuery($callbackId);
    }

    protected function getDialogStoragePath($dialogName)
    {
        $baseStoragePath = Registry::getInstance()->getApp()['baseStoragePath'];
        return $baseStoragePath . DIRECTORY_SEPARATOR . 'dialogs' . DIRECTORY_SEPARATOR . $dialogName;
    }
}
