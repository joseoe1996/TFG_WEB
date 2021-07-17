<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Service\httpClient;
use Symfony\Component\Filesystem\Filesystem;

class UploadController extends AbstractController {

    /**
     * @Route("/inicio/upload", name="upload")
     */
    public function index(): Response {
        return $this->render('upload/index.html.twig', [
                    'controller_name' => 'UploadController',
        ]);
    }

    /**
     * @Route("/inicio/subir", name="subir")
     */
    public function subir(Request $request, FileUploader $uploader, httpClient $client) {

        $archivo = $request->files->get('formFile');
        $nombreFichero = $uploader->upload($archivo);
        $origen = 'Users/josealonso/Desktop/TFG_WEB/public/uploads/' . $nombreFichero;
        $conexion = '113485777647440128669_drive';
        $response = $client->copiar_subir($origen, $conexion, $nombreFichero);

        if ($response->getStatusCode() == 200) {
            $filesystem = new Filesystem();
            $filesystem->remove($uploader->getTargetDirectory() . $nombreFichero);
        }
       
         return $this->redirectToRoute('lista_archivos');
    }

}
