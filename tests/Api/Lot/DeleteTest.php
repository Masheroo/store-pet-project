<?php

namespace Api\Lot;

use App\DataFixtures\UserFixture;
use App\Entity\Lot;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Service\Manager\LocalImageManager;
use Doctrine\ORM\NonUniqueResultException;
use League\Flysystem\FilesystemException;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteTest extends WebTestCase
{
    /**
     * @throws NonUniqueResultException
     * @throws FilesystemException
     */
    #[DataProvider('provideUsers')]
    public function testDeleteLotSuccessful(User $user): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $client->loginUser($user);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->getFirst();

        $lotImageFilepath = $container->getParameter('upload_directory').DIRECTORY_SEPARATOR.$lot->getImage();

        /** @var string $testFilesDir */
        $testFilesDir = $container->getParameter('test_files_dir');
        $testFilePath = $testFilesDir.DIRECTORY_SEPARATOR.'test.png';

         /** @var LocalImageManager $imageManager */
        $imageManager = $container->get(LocalImageManager::class);
        $imageManager->copyToUploadDir($testFilePath, $lot->getImage());
        self::assertFileExists($lotImageFilepath);

        $client->request('DELETE', '/api/lot');
        self::assertResponseIsSuccessful();

        $deletedLot = $lotRepository->find($lot->getId());
        self::assertNull($deletedLot);
        self::assertFileDoesNotExist($lotImageFilepath);
    }

    public static function provideUsers(): array
    {
        $container = self::getContainer();
        return [
            self::getUser($container, User::ROLE_ADMIN),
            self::getUser($container, User::ROLE_MANAGER)
        ];
    }

    private static function getUser(ContainerInterface $container, string $role): User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        return $userRepository->findOneBy(['roles' => [$role]]);
    }
}