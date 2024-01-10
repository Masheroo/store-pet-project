<?php

namespace App\Service\Discount;

use App\Entity\City;
use App\Entity\Discount\CityDiscount;
use App\Entity\Discount\UserDiscount;
use App\Entity\Discount\VolumeDiscount;
use App\Entity\Lot;
use App\Entity\LotDiscount;
use App\Entity\User;
use App\Repository\VolumeDiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class DiscountService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VolumeDiscountRepository $volumeDiscountRepository
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

    public function createUserDiscount(User $user, float $discount): UserDiscount
    {
        $userDiscount = new UserDiscount($user, $discount);
        $this->persistAndFlush($userDiscount);

        return $userDiscount;
    }

    public function createLotDiscount(Lot $lot, float $discount, int $countOfPurchases): LotDiscount
    {
        $lotDiscount = new LotDiscount($countOfPurchases, $lot, $discount);
        $this->persistAndFlush($lotDiscount);

        return $lotDiscount;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function computeUserDiscount(User $user): float
    {
        $totalUserVolume = $user->computeSumOfAllOrders();

        $volumeDiscount = $this->volumeDiscountRepository->findBiggestDiscountGreaterThan($totalUserVolume)?->getDiscount() ?? 0;
        $cityDiscount = $user->getCity()->getTotalDiscount();
        $personalUserDiscount = $user->computeSumOfAllUserDiscounts();

        return $volumeDiscount + $cityDiscount + $personalUserDiscount;
    }

    public function computeLotDiscount(Lot $lot, int $quantity): float
    {
        foreach ($lot->getLotDiscounts() as $discount) {
            if ($discount->getCountOfPurchases() <= $quantity){
                $totalDiscount = $discount->getDiscount();
            }
        }

        return $totalDiscount ?? 0;
    }
}