<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\onedriveToken;
use App\Entity\Conexiones;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

const IP = '127.0.0.1';
const PUERTO = '5572';
const DIR = IP . ':' . PUERTO;

class httpClient extends AbstractController {

    private $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    //Listar todos los archivos de una conexion
    public function lista(string $nombre, string $remote) {
        $response = $this->client->request('POST', 'http://' . DIR . '/operations/list', [
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
        $carpeta = array();
        $archivos = array();
        foreach ($lista as $arch) {
            foreach ($arch as $value) {
                if ($value['IsDir'] == 1) {
                    $carpeta[$value['Name']] = preg_replace('~/~', '_', $value['Path']);
                } else {
                    $archivos[$value['Name']] = $value['Path'];
                }
            }
        }
        $res = array('carpeta' => $carpeta, 'archivos' => $archivos);
        return $res;
    }

    //Crear la conexion onedrive en RCLONE
    public function onedrive() {
        //Crear el token de validacion y el nombre de la conexion
        $objeto = new onedriveToken();
        $token = $objeto->obtenerToken();
        $token_modificado = $objeto->token($token);
        $id = $objeto->getID();
        $alias = $objeto->getName();
        $name = $id . "_onedrive";

        $json = array("config_is_local" => "false"
            , "config_refresh_token" => "false"
            , "client_id" => '088e81a1-5274-44dd-bae8-fe657686b19f'
            , "client_secret" => 'Ag4.cX~HE-x27aLO8W.9a~rZ77e_iqR3H_'
            , "region" => 'global'
            , "drive_id" => $id
            , "drive_type" => 'personal'
            , "token" => $token_modificado
        );

        $response = $this->client->request('POST', 'http://' . DIR . '/config/create', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'name' => $name,
                'type' => 'onedrive',
                'obscure' => 'true',
                'parameters' => json_encode($json)
            ],
        ]);
        $conexion = new Conexiones();
        $conexion->setNombre($name);
        $conexion->setTipo('onedrive');
        $conexion->setUser($this->getUser());
        $conexion->setAlias($alias);
        //Base de datos
        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($conexion);
            $em->flush();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }
    }

}
