<?php

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\Discount\CityDiscount;
use App\Entity\Order;
use App\Repository\CityDiscountRepository;
use App\Type\DiscountType;

class CityDiscountService implements DiscountServiceInterface
{
    public const DISCOUNT_NAME = 'city';

    public function __construct(
        private readonly CityDiscountRepository $cityDiscountRepository
    ) {
    }

    public function computeDiscount(Order $order): array
    {
        $discounts = [];
        $cityDiscounts = $this->cityDiscountRepository->findBy(['city' => $order->getUser()->getCity()?->getId()]);
        foreach ($cityDiscounts as $cityDiscount) {
            $discounts[] = new Discount(
                self::DISCOUNT_NAME,
                DiscountType::Percent,
                $cityDiscount->getDiscount() ?? 0 * $order->getFullPrice(),
            );
        }

        return $discounts;
    }
}
