<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\onedriveToken;

class CrearConexionController extends AbstractController {

    /**
     * @Route("/inicio/lista_conexion", name="lista_conexion")
     */
    public function index(): Response {

        return $this->render('crear_conexion/index.html.twig', [
                    'controller_name' => 'CrearConexionController',
        ]);
    }

    /**
     * @Route("/inicio/crear_onedrive", name="crear_onedrive")
     */
    public function onedrive(): Response {
        
        //Usuario actual
        $userlog = $this->getUser()->getUsername();
        //Lista de conexiones del susario actual
        $conexiones = NULL;
        $objeto = new onedriveToken();

        $token = $objeto->obtenerToken();
        $token_modificado = $objeto->token($token);
        $id = $objeto->getId();
        $name = $objeto->getName();
     
        //Crear conexion hacia el rclone y guardar en la BD

            return $this->redirectToRoute('lista_conexion');

}
}
