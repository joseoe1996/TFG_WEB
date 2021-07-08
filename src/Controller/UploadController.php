<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;

class UploadController extends AbstractController
{
    /**
     * @Route("/inicio/upload", name="upload")
     */
    public function index(): Response
    {
        return $this->render('upload/index.html.twig', [
            'controller_name' => 'UploadController',
        ]);
    }
	
	/**
     * @Route("/inicio/subir", name="subir")
     */
	public function subir(Request $request, FileUploader $uploader){
		
		$archivo = $request->files->get('formFile');
		$nombreFichero = $uploader->upload($archivo);
		
		return new Response();
	}
}
