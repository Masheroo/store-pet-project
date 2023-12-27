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

    public const CITY_COUNT = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < self::CITY_COUNT; ++$i) {
            $this->cityService->create($faker->city());
        }
        $manager->flush();
    }
}
