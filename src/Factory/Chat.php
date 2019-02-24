<?php

namespace Inquirer\Factory;

use Inquirer;
use Inquirer\Storage;

class Chat
{
    private $baseStoragePath;

    public function __construct($baseStoragePath)
    {
        $this->baseStoragePath = $baseStoragePath;
    }

    public function create($chatId)
    {
        $storagePath = $this->getChatStoragePath($chatId);
        $storage = new Storage($storagePath);
        $data = new \stdClass();
        $data->conversation = [];
        $storage->set($data);
        $chat = new Inquirer\Chat($chatId, $storage);
        $chat->addButler();
        return $chat;
    }

    public function getById($chatId)
    {
        $storagePath = $this->getChatStoragePath($chatId);
        if (!file_exists($storagePath)) {
            throw new Inquirer\Exception\Exception("Chat with identity '{$chatId}' not exists");
        }
        return new Inquirer\Chat($chatId, new Storage($this->getChatStoragePath($chatId)));
    }

    public function findById($chatId)
    {
        try {
            return $this->getById($chatId);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getChatStoragePath($chatId)
    {
        return $this->baseStoragePath . DIRECTORY_SEPARATOR . 'chats' . DIRECTORY_SEPARATOR . $chatId;
    }
}
