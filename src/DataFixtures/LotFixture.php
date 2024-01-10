<?php

namespace App\DataFixtures;

use App\Entity\Lot;
use App\Repository\LotRepository;
use App\Service\Manager\LocalImageManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use League\Flysystem\FilesystemException;

class LotFixture extends Fixture
{
    public const COUNT_OF_LOTS = 10;

    public function __construct(
        private readonly LotRepository $repository,
        private readonly LocalImageManager $imageManager
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::COUNT_OF_LOTS; ++$i) {
            $this->repository->persist($this->createLotHelpsFaker($faker));
        }
        $manager->flush();
    }

    /**
     * @throws FilesystemException
     */
    public function createLotHelpsFaker(Generator $faker): Lot
    {
        return new Lot(
            $faker->company().', '.$faker->firstNameFemale().' '.$faker->randomDigitNotZero(),
            $faker->randomFloat(nbMaxDecimals: 2, min: 0, max: 10000000),
            $faker->randomDigitNotZero(),
            $this->imageManager->save(__DIR__.DIRECTORY_SEPARATOR.'blank-image.png')
        );
    }
}
