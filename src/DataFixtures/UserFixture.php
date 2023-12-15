<?php

namespace App\DataFixtures;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const EMAIL_USER = 'user@mail.ru';
    public const PASSWORD_USER = 'user.password';
    public const EMAIL_MANAGER = 'manager@mail.ru';
    public const PASSWORD_MANAGER = 'manager.password';

    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepository $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAndPersistUser(self::EMAIL_USER, self::PASSWORD_USER);
        $this->createAndPersistUser('user2@mail.ru', 'password2');

        $this->createAndPersistAdmin('admin@mail.ru', 'admin123');

        $this->createAndPersistManager(self::EMAIL_MANAGER, self::PASSWORD_MANAGER);

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
