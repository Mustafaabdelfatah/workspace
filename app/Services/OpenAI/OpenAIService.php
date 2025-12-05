<?php

namespace App\Services\OpenAI;

use GuzzleHttp\Client;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.openai.com/v1/']);
        $this->apiKey = config('services.openai.api_key');
    }

    public function sendMessage($message, $model = 'gpt-4o')
    {
        $response = $this->client->post('chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model'    => $model,
                'messages' => [['role' => 'user', 'content' => $message]],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
