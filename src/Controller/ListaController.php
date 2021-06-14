<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\httpClient;

class ListaController extends AbstractController {

    /**
     * @Route("/inicio/lista/{ruta}", name="lista_archivos")
     */
    public function index(httpClient $cliente, string $ruta=""): Response {
        // rclone rcd --rc-serve --rc-no-auth
        $userlog = $this->getUser()->getUsername();
        
       
        $final=preg_replace('/_/', '/', $ruta);
       
        $lista = $cliente->lista('jose:', $final);
        
        $separados=$cliente->separar($lista);
        
        $carpetas=$separados['carpeta'];
        $archivos=$separados['archivos'];
       
        
        return $this->render('lista/index.html.twig', [
                    'controller_name' => 'ListaController',
                    'carpetas' => $carpetas,
                    'archivos' => $archivos
        ]);
    }

}
