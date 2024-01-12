<?php

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\Discount\LotDiscount;
use App\Entity\Order;
use App\Repository\LotDiscountRepository;
use App\Type\DiscountType;

class LotDiscountService implements DiscountServiceInterface
{
    public const DISCOUNT_NAME = 'lot';

    public function __construct(
        private readonly LotDiscountRepository $lotDiscountRepository
    ) {
    }

    public function computeDiscount(Order $order): array
    {
        $lot = $order->getLot();
        $quantity = $order->getQuantity();

        $lotDiscounts = $this->lotDiscountRepository->findBy(['lot' => $lot->getId()]);

        $totalDiscount = [];

        foreach ($lotDiscounts as $discount) {
            if ($discount->getCountOfPurchases() <= $quantity) {
                $lotDiscount = $discount;
            }
        }

        if (isset($lotDiscount)) {
            $totalDiscount[0] = new Discount(
                self::DISCOUNT_NAME,
                $lotDiscount->getDiscount() * $order->getFullPrice(),
                ['value_type' => DiscountType::Percent->name, 'value' => $lotDiscount->getDiscount()]
            );
        }

        return $totalDiscount;
    }
}
