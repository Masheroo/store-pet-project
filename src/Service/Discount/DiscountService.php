<?php
declare(strict_types=1);

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\City;
use App\Entity\Discount\CityDiscount;
use App\Entity\Discount\LotDiscount;
use App\Entity\Discount\UserDiscount;
use App\Entity\Discount\VolumeDiscount;
use App\Entity\Lot;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\VolumeDiscountRepository;
use App\Type\DiscountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class DiscountService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VolumeDiscountRepository $volumeDiscountRepository,
        #[TaggedIterator('app.discount_services')]
        private readonly iterable $discountServices,
    ) {
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
     * @param Order $order
     * @return Discount[]
     */
    public function computeAllDiscountsForOrder(Order $order): array
    {
        $discounts = [];

        /** @var DiscountServiceInterface $discountService */
        foreach ($this->discountServices as $discountService) {
            $discounts = [...$discounts, ...$discountService->computeDiscount($order)];
        }

        return $discounts;
    }
}
