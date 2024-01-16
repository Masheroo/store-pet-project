<?php

namespace App\DataFixtures;

use App\Service\City\CityService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CityFixtures extends Fixture
{
    public function __construct(
        private readonly CityService $cityService
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->cityService->create(Factory::create()->city());
        $manager->flush();
    }
}
