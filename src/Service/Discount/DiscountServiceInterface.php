<?php

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\Order;

interface DiscountServiceInterface
{
    /** @return Discount[] */
    public function computeDiscount(Order $order): array;
}
