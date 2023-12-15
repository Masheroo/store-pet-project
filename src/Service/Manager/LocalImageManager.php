<?php

namespace App\Service\Manager;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalImageManager implements ImageManagerInterface
{
    public function __construct(
        private readonly string $uploadDir,
        private readonly FilesystemOperator $defaultStorage,
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function saveUploadedImage(UploadedFile $uploadedFile): string
    {
        $newFilename = uniqid().'.'.$uploadedFile->getExtension();
        $this->defaultStorage->move($uploadedFile->getRealPath(), $this->uploadDir.DIRECTORY_SEPARATOR.$newFilename);

        return $newFilename;
    }
}
