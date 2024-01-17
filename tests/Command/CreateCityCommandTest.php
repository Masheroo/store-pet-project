<?php

namespace App\Tests\Command;

use App\Repository\CityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateCityCommandTest extends KernelTestCase
{
    public const COMMAND = 'app:create:city';

    #[DataProvider('provideCityNames')]
    public function testExecuteSuccessful(array $cityNames): void
    {
        self::ensureKernelShutdown();
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find(self::COMMAND);
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([...$cityNames, '']);
        $commandTester->execute([], ['interactive']);

        $commandTester->assertCommandIsSuccessful();

        /** @var CityRepository $cityRepository */
        $cityRepository = self::getContainer()->get(CityRepository::class);
        $cities = $cityRepository->findAll();

        $savedCityNames = [];

        foreach ($cities as $city) {
            $savedCityNames[] = $city->getName();
        }

        foreach ($cityNames as $cityName) {
            self::assertContains($cityName, $savedCityNames);
        }
    }

    public static function provideCityNames(): iterable
    {
        return [
            [
                [
                    'City1'
                ]
            ],
            [
                [
                    'City2',
                    'City3',
                    'City4',
                    'City5'
                ]
            ]
        ];
    }
}
