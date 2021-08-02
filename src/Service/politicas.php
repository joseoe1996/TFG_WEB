<?php

namespace App\Service;

use App\Service\httpClient;
use App\Entity\Conexiones;

class politicas {

    private $client;

    public function __construct(httpClient $client) {
        $this->client = $client;
    }

    public function politicaDefecto($conexiones) {
        return $this->mayorEspacioLibre($conexiones);
    }

    public function mayorEspacioLibre($conexiones) {

        $max = 0;
        $eleccion = "";
        foreach ($conexiones as $conexion) {
            $actual = $this->client->about($conexion->getNombre())['free'];
            if ($actual > $max) {
                $max = $actual;
                $eleccion = $conexion->getNombre();
            }
        }
        return $eleccion;
    }

}
