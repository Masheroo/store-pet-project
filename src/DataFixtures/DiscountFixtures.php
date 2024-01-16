<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Discount\CityDiscount;
use App\Entity\Discount\LotDiscount;
use App\Entity\Discount\UserDiscount;
use App\Entity\Discount\VolumeDiscount;
use App\Entity\Lot;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Type\DiscountType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DiscountFixtures extends Fixture implements DependentFixtureInterface
{

    public const VOLUME_DISCOUNT_AMOUNT = 100000;
    public const VOLUME_DISCOUNT_VALUE = .1;
    public const PERCENT_USER_DISCOUNT_VALUE = .1;
    public const ABSOLUTE_USER_DISCOUNT_VALUE = 1000;
    public const CITY_DISCOUNT_VALUE = .1;
    public const LOT_DISCOUNT_COUNT_OF_PURCHASES = 1;
    public const LOT_DISCOUNT_DISCOUNT_VALUE = .1;

    public function load(ObjectManager $manager): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);
        $user = $userRepository->findByEmail(UserFixture::USER_EMAIL);


        $volumeDiscount = new VolumeDiscount(self::VOLUME_DISCOUNT_AMOUNT, self::VOLUME_DISCOUNT_VALUE);
        $manager->persist($volumeDiscount);

        $percentUserDiscount = new UserDiscount($user, self::PERCENT_USER_DISCOUNT_VALUE, DiscountType::Percent);
        $manager->persist($percentUserDiscount);
        $absoluteUserDiscount = new UserDiscount($user, self::ABSOLUTE_USER_DISCOUNT_VALUE, DiscountType::Percent);
        $manager->persist($absoluteUserDiscount);

        $city = $manager->getRepository(City::class)->findAll()[0];
        $cityDiscount = new CityDiscount($city, self::CITY_DISCOUNT_VALUE);
        $manager->persist($cityDiscount);

        $lotRepository = $manager->getRepository(Lot::class);
        $lot = $lotRepository->findAll()[2];
        $lotDiscount = new LotDiscount(self::LOT_DISCOUNT_COUNT_OF_PURCHASES,  $lot, self::LOT_DISCOUNT_DISCOUNT_VALUE);
        $manager->persist($lotDiscount);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            CityFixtures::class,
            LotFixture::class
        ];
    }
}
