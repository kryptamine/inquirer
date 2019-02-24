<?php

namespace Inquirer;

use Inquirer\Entity\ConversationItem;
use Longman\TelegramBot\Request;

class Api
{
    public function answerCallbackQuery($callbackId)
    {
        Request::answerCallbackQuery([
            'callback_query_id' => $callbackId,
            'text' => '',
        ]);
    }

    /**
     * @param $chatId
     * @param ConversationItem $conversationItem
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendMessage($chatId, ConversationItem $conversationItem)
    {
        $json = [
            'chat_id' => $chatId,
            'text' => $conversationItem->getMessage(),
            'parse_mode' => 'HTML',
        ];
        if ($conversationItem->hasOptions()) {
            $lines = [];
            foreach ($conversationItem->getOptions() as $option) {
                $buttons = [];
                $buttons[] = [
                    'text' => $option->getMessage(),
                    'callback_data' => $option->getKey(),
                ];
                $lines[] = $buttons;
            }

            if ($conversationItem->isMulti()) {
                $lines[] = [[
                    'text' => 'Submit',
                    'callback_data' => 'submit',
                ]];
            }
            $json['reply_markup'] = [
                'inline_keyboard' => $lines,
                'resize_keyboard' => true,
            ];
        }

        $response = Request::sendMessage($json);

        return $response->getResult()->message_id;
    }

    public function editMessageReplyMarkup($chatId, $messageId)
    {
        Request::editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => [
                'inline_keyboard' => [],
            ]
        ]);
    }
}
