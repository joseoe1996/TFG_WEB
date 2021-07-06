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

        $userlog = $this->getUser()->getId();

        $final = preg_replace('/_/', '/', $ruta);
        $lista = array();
        $alias = array();

        if (!empty($conexion)) {
            $criteria = ['nombre' => $conexion];
            $conexiones_BD = $conerepo->findBy($criteria);
        } else {
            $criteria = ['user' => $userlog];
            $conexiones_BD = $conerepo->findBy($criteria);
        }
        foreach ($conexiones_BD as $array) {
            // var_dump($array->getAlias());
            $archivos_asociados = $cliente->lista($array->getNombre(), $final);
            $separados = $cliente->separar($archivos_asociados);
            $carpetas = $separados['carpeta'];
            $archivos = $separados['archivos'];
            $lista[$array->getNombre()] = ['carpetas' => $carpetas, 'archivos' => $archivos];
            $alias[$array->getNombre()] = $array->getAlias();
        }

        return $this->render('lista/index.html.twig', [
                    'controller_name' => 'ListaController',
                    'lista' => $lista,
                    'alias' => $alias
        ]);
    }

    /**
     * @Route("/inicio/lista_borrar_archivo/{conexion}/{ruta}", name="borrar_archivo")
     */
    public function borrarARCH(httpClient $client, string $ruta = "", string $conexion = "") {

        $ruta2 = preg_replace('/_/', '/', $ruta);
        $client->borrarARCH($conexion, $ruta2);
        return $this->redirectToRoute('lista_archivos');
    }

    /**
     * @Route("/inicio/lista_borrar_carpeta/{conexion}/{ruta}", name="borrar_carpeta")
     */
    public function borrarCARP(httpClient $client, string $ruta = "", string $conexion = "") {

        $ruta2 = preg_replace('/_/', '/', $ruta);
        $client->borrarCARP($conexion, $ruta2);
        return $this->redirectToRoute('lista_archivos');
    }

}
