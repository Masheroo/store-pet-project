<?php

namespace App\DataFixtures;

use App\Entity\Lot;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Service\Manager\LocalImageManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use League\Flysystem\FilesystemException;

class LotFixture extends Fixture implements DependentFixtureInterface
{
    public const COUNT_OF_LOTS = 10;

    public function __construct(
        private readonly LotRepository $repository,
        private readonly LocalImageManager $imageManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $userManager = $this->userRepository->findOneBy(['email' => UserFixture::MANAGER_EMAIL]);

        for ($i = 0; $i < self::COUNT_OF_LOTS; ++$i) {
            $this->repository->persist($this->createLotHelpsFaker($faker, $userManager));
        }
        $manager->flush();
    }

    /**
     * @throws FilesystemException
     */
    public function createLotHelpsFaker(Generator $faker, User $manager): Lot
    {
        return new Lot(
            $faker->company().', '.$faker->firstNameFemale().' '.$faker->randomDigitNotZero(),
            $faker->randomFloat(nbMaxDecimals: 2, min: 0, max: 10000000),
            $faker->randomDigitNotZero(),
            $this->imageManager->save(__DIR__.DIRECTORY_SEPARATOR.'blank-image.png'),
            $manager
        );
    }

    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }
}
