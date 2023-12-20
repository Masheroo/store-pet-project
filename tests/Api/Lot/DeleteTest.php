<?php

namespace Api\Lot;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Service\Manager\LocalImageManager;
use Doctrine\ORM\NonUniqueResultException;
use League\Flysystem\FilesystemException;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteTest extends WebTestCase
{
    /**
     * @throws NonUniqueResultException
     * @throws FilesystemException
     * @covers \App\Controller\LotController::delete()
     */
    #[DataProvider('provideUsers')]
    public function testDeleteSuccessful(User $user): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();

        $client->loginUser($user);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->getFirst();
        $lotId = $lot->getId();

        $lotImageFilepath = $container->getParameter('upload_directory').DIRECTORY_SEPARATOR.$lot->getImage();

        /** @var string $testFilesDir */
        $testFilesDir = $container->getParameter('test_files_dir');
        $testFilePath = $testFilesDir.DIRECTORY_SEPARATOR.'test-image.png';

         /** @var LocalImageManager $imageManager */
        $imageManager = $container->get(LocalImageManager::class);
        $imageManager->copyToUploadDir($testFilePath, $lot->getImage());
        self::assertFileExists($lotImageFilepath);

        $client->request('DELETE', '/api/lot/'.$lotId);
        self::assertResponseIsSuccessful();

        $deletedLot = $lotRepository->find($lotId);
        self::assertNull($deletedLot);
        self::assertFileDoesNotExist($lotImageFilepath);
    }

    public static function provideUsers(): array
    {
        $container = self::getContainer();
        $userRepository = $container->get(UserRepository::class);
        return [
            [
                $userRepository->findOneBy(['email' => UserFixture::ADMIN_EMAIL])
            ],
            [
                $userRepository->findOneBy(['email' => UserFixture::MANAGER_EMAIL])
            ]
        ];
    }
}