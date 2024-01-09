<?php

namespace App\Service\Discount;

use App\Entity\City;
use App\Entity\CityDiscount;
use App\Entity\VolumeDiscount;
use Doctrine\ORM\EntityManagerInterface;

class DiscountService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    private function persistAndFlush(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function createVolumeDiscount(int $amount, float $discount): VolumeDiscount
    {
        $volumeDiscount = new VolumeDiscount($amount, $discount);
        $this->persistAndFlush($volumeDiscount);

        return $volumeDiscount;
    }

    public function createCityDiscount(City $city, float $discount): CityDiscount
    {
        $cityDiscount = new CityDiscount($city, $discount);
        $this->persistAndFlush($cityDiscount);

        return $cityDiscount;
    }
}