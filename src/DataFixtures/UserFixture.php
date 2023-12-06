<?php

namespace App\DataFixtures;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepository $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAndPersistUser('user1@mail.ru', 'password1');
        $this->createAndPersistUser('user2@mail.ru', 'password2');

        $this->createAndPersistAdmin('admin1@mail.ru', 'admin123');
        $this->createAndPersistAdmin('admin2@mail.ru', 'admin321');

        $manager->flush();
    }

    private function createAndPersistUser(string $email, string $password): void
    {
        $user = $this->userService->createUser(
            $email,
            $password
        );

        $this->userRepository->persist($user);
    }

    private function createAndPersistAdmin(string $email, string $password): void
    {
        $admin = $this->userService->createAdmin(
            $email,
            $password
        );

        $this->userRepository->persist($admin);
    }
}
