<?php

namespace App\Tests\Service\Manager;

use App\Service\Manager\LocalImageManager;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LocalImageManagerTest extends KernelTestCase
{
    /**
     * @throws FilesystemException
     */
    public function testSave(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        /** @var string $testFilesDir */
        $testFilesDir = $container->getParameter('test_files_dir');
        $testFilePath = $testFilesDir.DIRECTORY_SEPARATOR.'test.png';
        assert(file_exists($testFilePath));

        /** @var LocalImageManager $localImageManager */
        $localImageManager = $container->get(LocalImageManager::class);
        $newFilename = $localImageManager->save($testFilePath);

        /** @var string $uploadDirectory */
        $uploadDirectory = $container->getParameter('upload_directory');

        $savedFilePath = $uploadDirectory.DIRECTORY_SEPARATOR.$newFilename;
        self::assertFileExists($savedFilePath);
        self::assertFileEquals($testFilePath, $savedFilePath);
    }
}
