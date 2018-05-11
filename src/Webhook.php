<?php

namespace Inquirer;

use Inquirer\Entity;
use Inquirer\Exception;
use Inquirer\Factory;
use Inquirer\Bridge;

class Webhook
{
    public function process($botUsername, $data)
    {
        $log = Registry::getInstance()->getLog();
        $app = Registry::getInstance()->getApp();

        $log->info("Process incoming message");

        $chatId = $this->getChatId($data);
        $chatFactory = new Factory\Chat($app['baseStoragePath']);
        $chat = $chatFactory->findById($chatId);
        if (is_null($chat)) {
            $log->info("Create new chat '{$chatId}' with bot '{$botUsername}'");
            $chat = $chatFactory->create($chatId, $botUsername);
        } else {
            $log->info("Use existing chat '{$chatId}' with bot '{$botUsername}'");
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
        $log->info("Current conversation item '{$conversationItem->code}' with type '{$conversationItem->type}'");

        if ("/start" != $message && "butler" == $conversationItem->type) {
            $chat->pickUp($message);
            $email = $chat->getEmail();
            if (is_null($email)) {
                $chatBridge->keepConversation($conversationItem);
                return;
            }
            $chat->goToNextConversationItem();
        }

        if ("/start" != $message && "dispatcher" == $conversationItem->type) {
            $dialogCode = null;
            foreach ($conversationItem->options as $option) {
                if ($message == $option->code) {
                    $dialogCode = $message;
                    break;
                }
            }
            if (!is_null($dialogCode)) {
                $chat->addDialog($dialogCode);
            } else {
                $chatBridge->keepConversation($conversationItem);
                return;
            };
        }

        if ("/start" != $message) {
            $chat->addAnswer($message);
            if (isset($conversationItem->options)) {
                $chatBridge->removeOptions($conversationItem);
            }
            $conversationItem = $chat->goToNextConversationItem();
        }

        while ("info" == $conversationItem->type) {
            $chatBridge->keepConversation($conversationItem);
            $conversationItem = $chat->goToNextConversationItem();
        }

        $chatBridge->keepConversation($conversationItem);
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

    protected function getQuestion()
    {
        return new Entity\Question(
            'question1',
            'Choice correct answer!',
            [
                new Entity\Option('var1', 'May be yes?', 0),
                new Entity\Option('var1', 'May be no?', 0),
                new Entity\Option('var1', 'I don\'t know!', 100),
            ]
        );
    }
}
