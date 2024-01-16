<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture implements DependentFixtureInterface
{
    public const USER_EMAIL = 'user@mail.ru';
    public const USER_PASSWORD = 'user.password';
    public const MANAGER_EMAIL = 'manager@mail.ru';
    public const MANAGER_PASSWORD = 'manager.password';

    public const ADMIN_EMAIL = 'admin@mail.ru';

    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepository $userRepository,
        private readonly CityRepository $cityRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $city = $this->cityRepository->findAll()[0] ?? throw new \DomainException('Not one city found');

        $this->createAndPersistUser(self::USER_EMAIL, self::USER_PASSWORD, $city);
        $this->createAndPersistAdmin(self::ADMIN_EMAIL, 'admin123', $city);
        $this->createAndPersistManager(self::MANAGER_EMAIL, self::MANAGER_PASSWORD, $city);

        $manager->flush();
    }

    private function createAndPersistUser(string $email, string $password, City $city): void
    {
        $user = $this->userService->createUser($email, $password, $city);
        $user->replenishBalance(100000000);
        $this->userRepository->persist($user);
    }

    private function createAndPersistAdmin(string $email, string $password, City $city): void
    {
        $user = $this->userService->createAdmin($email, $password, $city);
        $user->replenishBalance(100000000);
        $this->userRepository->persist($user);
    }

    private function createAndPersistManager(string $email, string $password, City $city): void
    {
        $user = $this->userService->createManager($email, $password, $city);
        $user->replenishBalance(100000000);
        $this->userRepository->persist($user);
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class
        ];
    }
}
