<?php

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\City;
use App\Entity\Discount\CityDiscount;
use App\Entity\Discount\UserDiscount;
use App\Entity\Discount\VolumeDiscount;
use App\Entity\Lot;
use App\Entity\LotDiscount;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\VolumeDiscountRepository;
use App\Type\DiscountType;
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

    public function createUserDiscount(User $user, float $discount, DiscountType $type): UserDiscount
    {
        $userDiscount = new UserDiscount($user, $discount, $type);
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
    public function computeCityDiscount(User $user): Discount
    {
        $totalDiscount = new Discount(0, 0);
        $totalDiscount->percentDiscount = $user->getCity()?->getTotalDiscount() ?? 0;

        return $totalDiscount;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function computeVolumeDiscount(User $user): Discount
    {
        $totalDiscount = new Discount(0, 0);

        $totalUserVolume = $user->computeSumOfAllOrders();
        $totalDiscount->percentDiscount = $this->volumeDiscountRepository->findBiggestDiscountByAmount($totalUserVolume)?->getDiscount() ?? 0;

        return $totalDiscount;
    }

    public function computePersonalUserDiscount(User $user): Discount
    {
        $personalUserDiscounts = $user->getDiscounts();
        $totalDiscount = new Discount(0, 0);

        /** @var UserDiscount $discount */
        foreach ($personalUserDiscounts as $discount) {
            switch ($discount->getType()) {
                case DiscountType::Absolute:
                    $totalDiscount->absoluteDiscount += $discount->getDiscount() ?? 0;
                    break;
                case DiscountType::Percent:
                    $totalDiscount->percentDiscount += $discount->getDiscount() ?? 0;
                    break;
            }
        }

        return $totalDiscount;
    }

    public function computeLotDiscount(Lot $lot, int $quantity): Discount
    {
        $totalDiscount = new Discount(0, 0);

        foreach ($lot->getLotDiscounts() as $discount) {
            if ($discount->getCountOfPurchases() <= $quantity) {
                $totalDiscount->percentDiscount = $discount->getDiscount();
            }
        }

        return $totalDiscount;
    }

    /**
     * @param Order $order
     * @return Discount
     * @throws NonUniqueResultException
     */
    public function computeFullDiscountByOrder(Order $order): Discount
    {
        return (new Discount(0, 0))
            ->increase($this->computeVolumeDiscount($order->getUser()))
            ->increase($this->computePersonalUserDiscount($order->getUser()))
            ->increase($this->computeLotDiscount($order->getLot(), $order->getQuantity()))
            ->increase($this->computeCityDiscount($order->getUser()));

    }

    public function calculateDiscount(float $cost, Discount $discount): float
    {
        return $discount->absoluteDiscount + $cost * $discount->percentDiscount;
    }
}
