<?php

namespace Inquirer;

use GuzzleHttp;

class Api
{
    private $httpClient;

    public function answerCallbackQuery($token, $callbackId)
    {
        $this->getHttpClient()->post("/bot{$token}/answerCallbackQuery", [
            'json' => [
                'callback_query_id' => $callbackId,
                'text' => '',
            ],
        ]);
    }

    public function sendMessage($token, $chatId, $message, $options = [])
    {
        $json = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];
        if (!empty($options)) {
            $lines = [];
            foreach ($options as $option) {
                $buttons = [];
                $buttons[] = [
                    'text' => $option->message,
                    'callback_data' => $option->code,
                ];
                $lines[] = $buttons;
            }
            $json['reply_markup'] = [
                'inline_keyboard' => $lines,
                'resize_keyboard' => true,
            ];
        }
        $response = $this->getHttpClient()->post("/bot{$token}/sendMessage", [
            'json' => $json,
        ]);
        $data = json_decode($response->getBody());
        return $data->result->message_id;
    }

    public function editMessageReplyMarkup($token, $chatId, $messageId)
    {
        $this->getHttpClient()->post("/bot{$token}/editMessageReplyMarkup", [
            'json' => [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'reply_markup' => [
                    'inline_keyboard' => [],
                ]
            ],
        ]);
    }

    public function registerWebhook($token, $webhookUrl)
    {
        $this->getHttpClient()->get("/bot{$token}/setWebhook?url={$webhookUrl}");
    }

    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new GuzzleHttp\Client([
                'base_uri' => 'https://api.telegram.org',
                'timeout' => 2,
            ]);
        }
        return $this->httpClient;
    }
}
