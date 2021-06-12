<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\httpClient;

class ListaController extends AbstractController
{
    /**
     * @Route("/inicio/lista", name="lista")
     */
    public function index(httpClient $cliente): Response
    {
        // rclone rcd --rc-serve --rc-no-auth
       $userlog=$this->getUser()->getUsername();

       $content=$cliente->lista('jose:');
      // var_dump($userlog);
      // var_dump($statusCode);
      // var_dump($contentType);
      print_r($content);
      
        return $this->render('lista/index.html.twig', [
            'controller_name' => 'ListaController',
        ]);
    }
}
