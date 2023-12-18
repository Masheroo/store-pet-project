<?php

namespace Api\Lot;

use App\DataFixtures\UserFixture;
use App\Entity\Lot;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteTest extends WebTestCase
{
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

        self::assertFileExists($lotImageFilepath);

        $client->request('DELETE', '/api/lot');

        $deletedLot = $lotRepository->find($lot->getId());

        self::assertResponseIsSuccessful();
        self::assertNull($deletedLot);
        self::assertFileDoesNotExist($lotImageFilepath);
    }

    public function provideUsers(): array
    {
        $container = self::getContainer();
        return [
            $this->getUser($container, User::ROLE_ADMIN),
            $this->getUser($container, User::ROLE_MANAGER)
        ];
    }

    private function getUser(ContainerInterface $container, string $role): User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        return $userRepository->findOneBy(['roles' => [$role]]);
    }
}