<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Service\httpClient;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $destino = '113485777647440128669_drive:';
        $response = $client->copiar_subir($origen, $destino, $nombreFichero);

        if ($response->getStatusCode() == 200) {
            $filesystem = new Filesystem();
            $filesystem->remove($uploader->getTargetDirectory() . $nombreFichero);
        }


        return $this->redirectToRoute('lista_archivos');
    }

    /**
     * @Route("/inicio/bajar/{conexion}/{ruta}", name="bajar", requirements={"ruta"=".+"})
     */
    public function bajar(FileUploader $uploader, httpClient $client, $ruta, $conexion) {

        $archivo = preg_split("[/]", $ruta);
        $nombreArchivo= array_pop($archivo);
        $file = 'Users/josealonso/Desktop/TFG_WEB/public/uploads/' . $nombreArchivo;
        $client->copiar_bajar($conexion . ':', $file, $ruta);
       
        $destino = $uploader->getTargetDirectory() . $nombreArchivo;

        $response = new BinaryFileResponse($destino);

        $disposition = HeaderUtils::makeDisposition(
                        HeaderUtils::DISPOSITION_ATTACHMENT, $nombreArchivo
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->deleteFileAfterSend(true);
        return $response;
    }

}
