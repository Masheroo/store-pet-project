<?php

namespace App\DataFixtures;

use App\Entity\AccessRight;
use App\Repository\UserRepository;
use App\Security\AccessValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccessRightsFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userManager = $this->userRepository->findOneBy(['email' => UserFixture::MANAGER_EMAIL]);

        $createLotAccessRight = new AccessRight($userManager, AccessValue::CreateLot);
        $updateLotAccessRight = new AccessRight($userManager, AccessValue::UpdateOwnLot);
        $deleteLotAccessRight = new AccessRight($userManager, AccessValue::DeleteOwnLot);

        $manager->persist($createLotAccessRight);
        $manager->persist($updateLotAccessRight);
        $manager->persist($deleteLotAccessRight);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
          UserFixture::class
        ];
    }
}
