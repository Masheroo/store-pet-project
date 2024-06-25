<?php

namespace App\Dto;

use App\Type\DiscountType;

class Discount
{
    public function __construct(
        public string $discountName,
        public DiscountType $type,
        public float $discount = 0,
    ) {
    }
}
