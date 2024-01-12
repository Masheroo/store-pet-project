<?php

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\Discount\UserDiscount;
use App\Entity\Order;
use App\Type\DiscountType;

class PersonalUserDiscountService implements DiscountServiceInterface
{
    public const DISCOUNT_NAME = 'personal';

    public function computeDiscount(Order $order): array
    {
        $user = $order->getUser();

        $personalUserDiscounts = $user->getDiscounts();
        $discounts = [];

        /** @var UserDiscount $discount */
        foreach ($personalUserDiscounts as $discount) {
            $discounts[] = new Discount(
                self::DISCOUNT_NAME,
                match ($discount->getType()) {
                    DiscountType::Percent => $discount->getDiscount() * $order->getFullPrice(),
                    DiscountType::Absolute => $discount->getDiscount(),
                },
                ['value_type' => $discount->getType()->name, 'value' => $discount->getDiscount()]
            );
        }

        return $discounts;
    }
}
