<?php

namespace App\Tests\Service\City;

use App\Entity\City;
use App\Service\City\CityService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityServiceTest extends KernelTestCase
{
    private const CITY_NAME = 'TestCity';

    public function testCreate(): void
    {
        self::bootKernel();
        /** @var CityService $service */
        $service = self::getContainer()->get(CityService::class);

        $city = $service->create(self::CITY_NAME);

        self::assertNotNull($city);
        self::assertEquals(self::CITY_NAME, $city->getName());
    }
}
