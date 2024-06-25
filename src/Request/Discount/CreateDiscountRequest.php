<?php

namespace App\Request\Discount;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateDiscountRequest
{
    #[NotBlank]
    #[LessThan(1)]
    #[GreaterThan(0)]
    public float $discount;
}