<?php

namespace App\Request\Discount;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateLotDiscountRequest extends CreateDiscountRequest
{
    #[NotBlank]
    #[GreaterThan(0)]
    public int $countOfPurchases;
}