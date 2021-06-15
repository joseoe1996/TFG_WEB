<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\httpClient;
use App\Repository\ConexionesRepository;

class ListaController extends AbstractController {

    /**
     * @Route("/inicio/lista/{conexion}/{ruta}", name="lista_archivos")
     */
    public function index(httpClient $cliente, ConexionesRepository $conerepo, string $ruta = "", string $conexion = ""): Response {
        // rclone rcd --rc-serve --rc-no-auth
       
        $userlog = $this->getUser()->getId();
      


        $final = preg_replace('/_/', '/', $ruta);
        $lista = array();

        if (!empty($conexion)) {
            $criteria = ['nombre' => $conexion];
            $conexiones_BD = $conerepo->findBy($criteria);
        } else {
            $criteria = ['user' => $userlog];
            $conexiones_BD = $conerepo->findBy($criteria);
        }
        foreach ($conexiones_BD as $array) {
            $archivos_asociados = $cliente->lista($array->getNombre() . ":", $final);
            $separados = $cliente->separar($archivos_asociados);
            $carpetas = $separados['carpeta'];
            $archivos = $separados['archivos'];
            $lista[$array->getNombre()] = ['carpetas' => $carpetas, 'archivos' => $archivos];
        }


        return $this->render('lista/index.html.twig', [
                    'controller_name' => 'ListaController',
                    'lista' => $lista
        ]);

        // return new Response();
    }

}
