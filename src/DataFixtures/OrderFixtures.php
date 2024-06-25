<?php

namespace App\DataFixtures;

use App\Entity\Lot;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => UserFixture::USER_EMAIL]);

        $lotRepository = $manager->getRepository(Lot::class);
        $lot = $lotRepository->findAll()[0];

        $order = new Order($user, $lot, 1);

        $manager->persist($order);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            LotFixture::class
        ];
    }
}
