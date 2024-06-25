<?php

namespace App\Tests\Api\Lot;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Tests\Helpers\FileSystemHelper;
use App\Tests\Traits\ClientConfiguratorTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateTest extends WebTestCase
{
    use ClientConfiguratorTrait;

    public const TITLE = 'ТЕСТОВЫЙ ЛОТ';
    public const COST = 1000000;
    public const COUNT = 5;

    /**
     * @covers \App\Controller\LotController::createLot
     */
    public function testCreateSuccessful(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();
        /** @var FileSystemHelper $imageManager */
        $imageManager = $container->get(FileSystemHelper::class);

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => UserFixture::MANAGER_EMAIL]);
        $client->loginUser($user);

        /** @var string $testFilesDir */
        $testFilesDir = $container->getParameter('test_files_dir');
        $filePath = $testFilesDir.DIRECTORY_SEPARATOR.'test.png';
        copy($testFilesDir.DIRECTORY_SEPARATOR.'test-image.png', $filePath);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findAll()[0];

        $client->request(
            'POST',
            '/api/lot',
            parameters: [
                'title' => self::TITLE,
                'cost' => self::COST,
                'count' => self::COUNT,
                'category' => $category->getId()
            ],
            files: [
                'image' => $uploadedFile = new UploadedFile(
                    path: $filePath, originalName: 'test-image.png', mimeType: 'image/png', test: true
                ),
            ]
        );

        self::assertResponseStatusCodeSame(200);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->findOneBy(['title' => self::TITLE]);

        self::assertNotEmpty($lot);

        /** @var string $uploadDirectory */
        $uploadDirectory = $container->getParameter('upload_directory');
        $savedFilename = $uploadDirectory.DIRECTORY_SEPARATOR.($lot->getImage() ?: '');
        self::assertFileExists($savedFilename);

        $savedFile = new \SplFileInfo($savedFilename);
        self::assertEquals($savedFile->getExtension(), $uploadedFile->getExtension());

        unlink($savedFilename);
        $lotRepository->deleteAndFlush($lot);
    }
}
