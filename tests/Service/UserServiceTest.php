<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\CityRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserServiceTest extends KernelTestCase
{

    public function testCreateManager()
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var UserService $service */
        $service = $container->get(UserService::class);

        /** @var CityRepository $cityRepository */
        $cityRepository = $container->get(CityRepository::class);
        $city = $cityRepository->findAll()[0];
        assert($city != null);

        $manager = $service->createManager('test_manager@mail.ru', 'password123', $city);

        self::assertContains(User::ROLE_MANAGER, $manager->getRoles());
        self::assertEquals('test_manager@mail.ru', $manager->getEmail());

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        self::assertTrue($passwordHasher->isPasswordValid($manager, 'password123'));
        self::assertEquals($city, $manager->getCity());
    }
}
