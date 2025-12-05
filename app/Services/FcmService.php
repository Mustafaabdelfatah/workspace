<?php
namespace App\Services;

use Google\Client as GoogleClient;

/**
 * A class defines fcm
 */
class FcmService
{
    private $client;
    private $jsonData;
    private $tempFile;
    private $accessToken;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $data = [
            'type' => config('services.firebase.type'),
            'project_id' => config('services.firebase.project_id'),
            'private_key_id' => config('services.firebase.private_key_id'),
            'private_key' => str_replace("\n", "\n", config('services.firebase.private_key')),
            'client_email' => config('services.firebase.client_email'),
            'client_id' => config('services.firebase.client_id'),
            'auth_uri' => config('services.firebase.auth_uri'),
            'token_uri' => config('services.firebase.token_uri'),
            'auth_provider_x509_cert_url' => config('services.firebase.auth_provider_x509_cert_url'),
            'client_x509_cert_url' => config('services.firebase.client_x509_cert_url'),
            'universe_domain' => config('services.firebase.universe_domain'),
        ];

        $this->jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $this->tempFile = tempnam(sys_get_temp_dir(), 'fcm');

        file_put_contents($this->tempFile, $this->jsonData);
        register_shutdown_function('unlink', $this->tempFile);

        $this->client->setAuthConfig($this->tempFile);
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->accessToken = $this->client->fetchAccessTokenWithAssertion();
    }

    public function sendNotification($token, $title, $description, $data = [])
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);

        $options['headers'] = [
            'Authorization' => 'Bearer ' . $this->accessToken['access_token'],
            'Content-Type' => 'application/json',
        ];
        $options['json'] = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $description,
                ],
                'data' => $data,
            ]
        ];

        try {
            $request = $client->request('POST', 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send', $options);

            return [
                'status' => true,
                'message' => 'Message is sent successfully .'
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
    }
}
