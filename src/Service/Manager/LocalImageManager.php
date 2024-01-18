<?php

namespace App\Service\Manager;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalImageManager
{
    public function __construct(
        private readonly FilesystemOperator $defaultStorage,
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function saveUploadedImage(UploadedFile $uploadedFile): string
    {
        $newFilename = uniqid().'.'.$uploadedFile->getClientOriginalExtension();
        $this->defaultStorage->write($newFilename, $uploadedFile->getContent());

        return $newFilename;
    }

    /**
     * Returns saved filename.
     *
     * @throws FilesystemException
     */
    public function save(string $pathToFile): string
    {
        $file = new File($pathToFile, true);
        $newFilename = uniqid().'.'.$file->getExtension();
        $this->defaultStorage->write($newFilename, $file->getContent());

        return $newFilename;
    }

    /**
     * @throws FilesystemException
     */
    public function copyToUploadDir(string $from, string $to): void
    {
        $file = new File($from, true);
        $this->defaultStorage->write($to, $file->getContent());
    }

    /**
     * @throws FilesystemException
     */
    public function deleteIfExists(string $filename): void
    {
        if (!$this->defaultStorage->fileExists($filename)) {
            return;
        }
        $this->delete($filename);
    }

    /**
     * @throws FilesystemException
     */
    public function delete(string $filename): void
    {
        $this->defaultStorage->delete($filename);
    }

    public function getPublicLink(string $filename): string
    {
        if (!$this->defaultStorage->fileExists($filename)) {
            throw new FileNotFoundException();
        }

        return $this->defaultStorage->publicUrl($filename);
    }
}
