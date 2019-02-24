<?php

namespace Inquirer;

use Inquirer\Exception;
use Inquirer\Factory;
use Inquirer\Bridge;

class Webhook
{
    /**
     * @param $data
     * @throws Exception\StorageException
     * @throws Exception\WebhookException
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function process($data)
    {
        $log = Registry::getInstance()->getLog();
        $app = Registry::getInstance()->getApp();

        $log->info("Process incoming message");

        $chatId = $this->getChatId($data);
        $chatFactory = new Factory\Chat($app['baseStoragePath']);
        $chat = $chatFactory->findById($chatId);
        if (is_null($chat)) {
            $log->info("Create new chat '{$chatId}''");
            $chat = $chatFactory->create($chatId);
        } else {
            $log->info("Use existing chat '{$chatId}'");
        }

        $chatBridge = new Bridge\Chat($chat, $app['api']);

        if (isset($data->callback_query->id)) {
            try {
                $chatBridge->confirmAnswer($data->callback_query->id);
            } catch (\Exception $e) {
                Registry::getInstance()->getLog()->error("Unable to confirm answer {$data->callback_query->id}");
            }
        }

        $message = $this->getMessage($data);
        $log->info("Process message '{$message}'");

        $conversationItem = $chat->getCurrentConversationItem();


        if ("butler" == $conversationItem->getType()) {
            if (!$chat->pickUp($message)) {
                $chatBridge->keepConversation($conversationItem);
                return;
            }
            $conversationItem = $chat->goToNextConversationItem();
            $chatBridge->keepConversation($conversationItem);
            return;
        }

        if ("dispatcher" == $conversationItem->getType()) {
            $dialogCode = null;
            foreach ($conversationItem->getOptions() as $option) {
                if ($message == $option->getKey()) {
                    $dialogCode = $message;
                    break;
                }
            }
            if (is_null($dialogCode)) {
                $chatBridge->keepConversation($conversationItem);
                return;
            }
            $chat->addDialog($dialogCode);
            $this->removeOptions($chatBridge, $conversationItem);
            $conversationItem = $chat->goToNextConversationItem();
            $chatBridge->keepConversation($conversationItem);
            return;
        }

        if ("question" == $conversationItem->getType()) {
            $chat->addAnswer($message);
            $this->removeOptions($chatBridge, $conversationItem);
            $conversationItem = $chat->goToNextConversationItem();
        }

        while ("info" == $conversationItem->getType()) {
            $chatBridge->keepConversation($conversationItem);
            $conversationItem = $chat->goToNextConversationItem();
        }

        $chatBridge->keepConversation($conversationItem);
    }

    protected function removeOptions(Bridge\Chat $chatBridge, $conversationItem)
    {
        if (isset($conversationItem->options)) {
            try {
                $chatBridge->removeOptions($conversationItem);
            } catch (\Exception $e) {
                Registry::getInstance()->getLog()->error("Unable to remove options of message {$conversationItem->messageId}");
            }
        }
    }

    protected function getMessage($data)
    {
        if (isset($data->message->text)) {
            return $data->message->text;
        } else if (isset($data->callback_query->data)) {
            return $data->callback_query->data;
        }
    }

    protected function getChatId($data)
    {
        if (isset($data->message->chat->id)) {
            return $data->message->chat->id;
        } else if ($data->callback_query->message->chat->id) {
            return $data->callback_query->message->chat->id;
        }
        throw new Exception\WebhookException("Unable to extract chat identity");
    }
}
