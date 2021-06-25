<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\httpClient;
use App\Repository\ConexionesRepository;
use App\Service\driveToken;

class CrearConexionController extends AbstractController {

    /**
     * @Route("/inicio/lista_conexion", name="lista_conexion")
     */
    public function index(ConexionesRepository $con): Response {

        $userlog = $this->getUser()->getId();

        //Lista de conexiones del susario actual
        $criteria = ['user' => $userlog];
        $conexiones = $con->findBy($criteria);

        return $this->render('crear_conexion/index.html.twig', [
                    'controller_name' => 'CrearConexionController',
                    'conexiones' => $conexiones
        ]);
    }

    /**
     * @Route("/inicio/crear_onedrive", name="crear_onedrive")
     */
    public function onedrive(httpClient $client): Response {
        $client->onedrive();
        return $this->redirectToRoute('lista_conexion');
    }

    /**
     * @Route("/inicio/crear_drive", name="crear_drive")
     */
    public function drive(httpClient $client): Response {

        $objeto = new driveToken();
        $token = $objeto->getToken();
        $token_final = $objeto->token($token);
       // var_dump($token_final);
        
        return new Response();
    }

}
