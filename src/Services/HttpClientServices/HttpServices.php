<?php

namespace App\Services\HttpClientServices;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpServices
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchGitHubInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }

    public function sendFireBaseNotification($message,$title,$tokens)
    {
        $response = $this->client->request(
            'POST',
            'https://fcm.googleapis.com/fcm/send',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'key=AAAA87Dvgns:APA91bF3RBC-ptEyqfsJytRLAp4W33g2RcVHeoXyQG4q4tsnmgPLO61o_GekU2McS6r8_00eIEbGOTOGHv8LnO0agL8AOsdDLm1hllAPQMc3iaZa7jRf9pzwKZquu8Z6yjWhfpx684uS'
                    ],
                'json' => ['registration_ids' => $tokens, 
            'notification' => ["body" => $message,"title" => $title]],
            ]
        );
              
        $content = $response;
        //dd($response->getContent());
        return $content;
    }

    public function sendRequest($base_url,$json,$headers,$method)
    {
        $response = $this->client->request(
            $method,
            $base_url,
            [
                'headers' => $headers,
                'json' => $json,
            ]
        );
              
        $content = $response;
        //dd($response->getContent());
        return $content;
    }
}
