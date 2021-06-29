<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\onedriveToken;
use App\Entity\Conexiones;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\driveToken;

const IP = '127.0.0.1';
const PUERTO = '5572';
const DIR = IP . ':' . PUERTO;
const CLIENT_ID_ONEDRIVE = '088e81a1-5274-44dd-bae8-fe657686b19f';
const SECRETO_ONEDRIVE = 'Ag4.cX~HE-x27aLO8W.9a~rZ77e_iqR3H_';
const CLIENTE_ID_DRIVE = '673961889608-7bhejsqnglluor9prgrb03e13g3s18mg.apps.googleusercontent.com';
const SECRETO_DRIVE = 'tzXjmMQkz1qZ90FNNDtl2XKy';

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

    //Crear la conexion onedrive
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
            , "client_id" => CLIENT_ID_ONEDRIVE
            , "client_secret" => SECRETO_ONEDRIVE
            , "region" => 'global'
            , "drive_id" => $id
            , "drive_type" => 'personal'
            , "token" => $token_modificado
        );
        //Creamos la conexion con RCLONE
        $response = $this->client->request('POST', 'http://' . DIR . '/config/create', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'name' => $name,
                'type' => 'onedrive',
                'obscure' => 'true',
                'parameters' => json_encode($json)
            ],
        ]);
        //Guardamos esa conexion en la BD
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

    //Crear conexion drive
    public function drive() {

        $objeto = new driveToken();
        $token = $objeto->getToken();
        $token_final = $objeto->token($token);

        $jwt = $token->getValues()['id_token'];
        $claves = preg_split('[\.]', $jwt);

        $alias = json_decode(base64_decode($claves[1]))->name;
        $name = json_decode(base64_decode($claves[1]))->sub . '_drive';

        $json = array("config_is_local" => "false"
            , "config_refresh_token" => "false"
            , "client_id" => CLIENTE_ID_DRIVE
            , "client_secret" => SECRETO_DRIVE
            , "token" => $token_final
        );
        //Creamos la conexion con RCLONE
        $response = $this->client->request('POST', 'http://' . DIR . '/config/create', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'name' => $name,
                'type' => 'drive',
                'obscure' => 'true',
                'parameters' => json_encode($json)
            ],
        ]);
        //Guardamos la informacion en la BD
        $conexion = new Conexiones();
        $conexion->setNombre($name);
        $conexion->setTipo('drive');
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

    public function sftp(string $IP, string $user, string $pass) {

        $json = array(
            "host" => $IP
            , "user" => $user
            , "pass" => $pass
            , "port" => 2222
        );
        $name = preg_replace('[\.]', '_', $IP);
        $response = $this->client->request('POST', 'http://' . DIR . '/config/create', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'name' => $name,
                'type' => 'sftp',
                'obscure' => 'true',
                'parameters' => json_encode($json)
            ],
        ]);

        //Guardamos la informacion en la BD
        $conexion = new Conexiones();
        $conexion->setNombre($name);
        $conexion->setTipo('sftp');
        $conexion->setUser($this->getUser());
        $conexion->setAlias($user);
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
