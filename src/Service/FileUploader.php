<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader {

    private $targetDirectory;
    private $slugger;
    private $dirPoliticas;

    public function __construct($targetDirectory, SluggerInterface $slugger, $dirPoliticas) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->dirPoliticas = $dirPoliticas;
    }

    public function upload(UploadedFile $file) {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function extension(UploadedFile $file, string $extension) {
        $arch_extension = $file->guessExtension();
        if ($arch_extension == $extension) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function tamaño(UploadedFile $file, int $tam) {
        $arch_tam = filesize($file);
        if ($arch_tam < $tam) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function ContieneNombre(UploadedFile $file, string $patron) {
        $arch_nombre = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if (preg_match($patron, $arch_nombre)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getTargetDirectory() {
        return $this->targetDirectory;
    }

    public function ListaPoliticas() {
        $nombre = 'politicas.json';
        $politicas = new UploadedFile($this->dirPoliticas . $nombre, $nombre);
        $json = json_decode($politicas->getContent());
        return (array) $json->politicas->id;
    }

}
