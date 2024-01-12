<?php

namespace App\Dto;

class Discount
{
    public function __construct(
        public string $discountName,
        public float $discount = 0,
        public array $info = []
    ) {
    }
}
