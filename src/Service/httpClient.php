<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class httpClient {

    private $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    //Listar todos los archivos de una conexion
    public function lista(string $nombre, string $remote) {
        $response = $this->client->request('POST', 'http://127.0.0.1:5572/operations/list', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'fs' => $nombre,
                'remote' => $remote,
            ],
        ]);
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        // $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        return $content;
    }

    //Dividir un directorio entre carpetas y archivos
    public function separar($lista) {
        $carpeta;
        $archivos;
        foreach ($lista as $arch) {
            foreach ($arch as $value) {
                if ($value['IsDir'] == 1) {
                    // var_dump('Es directorio: ' . $value['Name']);
                    $carpeta[] = $value['Name'];
                } else {
                    // var_dump('No es directorio: ' . $value['Name']);
                    $archivos[] = $value['Name'];
                }
            }
        }
        $res = array('carpeta'=>$carpeta, 'archivos'=>$archivos);
        return $res;
    }

}
