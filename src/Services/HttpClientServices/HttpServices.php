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
