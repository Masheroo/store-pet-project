<?php

namespace App\Service\Manager;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
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
    /**
     * Returns saved filename.
     * @param string $pathToFile
     * @return string
     * @throws FilesystemException
     */
    public function save(string $pathToFile): string
    {
        $file = new File($pathToFile, true);
        $newFilename = uniqid().'.'.$file->getExtension();
        $this->defaultStorage->move($file->getRealPath(), $this->uploadDir.DIRECTORY_SEPARATOR.$newFilename);
        return $newFilename;
    }

    /**
     * @throws FilesystemException
     */
    public function copyToUploadDir(string $from, string $newFilename): void
    {
        $this->defaultStorage->copy($from, $this->uploadDir.DIRECTORY_SEPARATOR.$newFilename);
    }

    /**
     * @throws FilesystemException
     */
    public function deleteIfExists(string $imageFilename): void
    {
        $filepath = $this->uploadDir.DIRECTORY_SEPARATOR.$imageFilename;
        if (!file_exists($filepath)){
            return;
        }
        $this->delete($filepath);
    }

    /**
     * @throws FilesystemException
     */
    public function delete(string $filepath): void
    {
        $this->defaultStorage->delete($filepath);
    }
}
