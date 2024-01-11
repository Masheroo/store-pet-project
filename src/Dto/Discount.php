<?php

namespace App\Dto;

class Discount
{
    public function __construct(
        public float $percentDiscount = 0,
        public float $absoluteDiscount = 0,
    ) {
    }

    public function increase(Discount $discount): self
    {
        $this->percentDiscount += $discount->percentDiscount;
        $this->absoluteDiscount += $discount->absoluteDiscount;

        return $this;
    }
}
