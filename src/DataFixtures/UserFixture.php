<?php

namespace App\DataFixtures;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const USER_EMAIL = 'user@mail.ru';
    public const USER_PASSWORD = 'user.password';
    public const MANAGER_EMAIL = 'manager@mail.ru';
    public const MANAGER_PASSWORD = 'manager.password';

    public const ADMIN_EMAIL = 'admin@mail.ru';

    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepository $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAndPersistUser(self::USER_EMAIL, self::USER_PASSWORD);
        $this->createAndPersistUser('user2@mail.ru', 'password2');

        $this->createAndPersistAdmin(self::ADMIN_EMAIL, 'admin123');

        $this->createAndPersistManager(self::MANAGER_EMAIL, self::MANAGER_PASSWORD);

        $manager->flush();
    }

    private function createAndPersistUser(string $email, string $password): void
    {
        $this->userRepository->persist($this->userService->createUser($email, $password));
    }

    private function createAndPersistAdmin(string $email, string $password): void
    {
        $this->userRepository->persist($this->userService->createAdmin($email, $password));
    }

    private function createAndPersistManager(string $email, string $password): void
    {
        $this->userRepository->persist($this->userService->createManager($email, $password));
    }
}
