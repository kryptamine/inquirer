<?php

namespace Inquirer;

use GuzzleHttp;

class Api
{
    private $httpClient;

    public function registerWebhook($token, $webhookUrl)
    {
        $this->getHttpClient()->get("/bot{$token}/setWebhook?url={$webhookUrl}");
    }

    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new GuzzleHttp\Client([
                'base_uri' => 'https://api.telegram.org',
            ]);
        }
        return $this->httpClient;
    }
}
