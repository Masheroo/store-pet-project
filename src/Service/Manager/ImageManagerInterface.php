<?php

namespace App\Service\Manager;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageManagerInterface
{
    /**
     * Returns saved filename.
     */
    public function saveUploadedImage(UploadedFile $uploadedFile): string;
}
