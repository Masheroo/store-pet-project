<?php

namespace App\Tests\Helpers;

use App\Service\Manager\FileManager;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\File;

class FileSystemHelper extends FileManager
{
    public function save(string $pathToFile): string
    {
        $file = new File($pathToFile, true);
        $newFilename = uniqid() . '.' . $file->getExtension();
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
}