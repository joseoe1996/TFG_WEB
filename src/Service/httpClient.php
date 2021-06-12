<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class httpClient
{
   private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function lista(string $nombre) {
        $response = $this->client->request('POST', 'http://127.0.0.1:5572/operations/list', [
    // these values are automatically encoded before including them in the URL
    'query' => [
        'fs' => $nombre,
        'remote' => '',
    ],
]);
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        
        return $content;
     
    }
    
    
}
