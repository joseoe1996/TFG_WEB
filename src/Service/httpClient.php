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
        $carpeta=array();
        $archivos=array();
        foreach ($lista as $arch) {
            foreach ($arch as $value) {
                if ($value['IsDir'] == 1) {
                     $carpeta[$value['Name']]= preg_replace('~/~', '_', $value['Path']);
                } else {
                    $archivos[$value['Name']]=$value['Path'];
                }
            }
        }
        $res = array('carpeta'=>$carpeta, 'archivos'=>$archivos);
        return $res;
    }

}
