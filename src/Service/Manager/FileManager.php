<?php

namespace App\Service\Manager;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    public function __construct(
        protected readonly FilesystemOperator $defaultStorage,
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function write(string $filename, string $content): void
    {
        $this->defaultStorage->write($filename, $content);
    }

    /**
     * @throws FilesystemException
     */
    public function saveUploadedImage(UploadedFile $uploadedFile): string
    {
        $newFilename = uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        $this->defaultStorage->write($newFilename, $uploadedFile->getContent());

        return $newFilename;
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
        return $this->defaultStorage->publicUrl($filename);
    }

}
