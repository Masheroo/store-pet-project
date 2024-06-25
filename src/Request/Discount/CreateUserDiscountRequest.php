<?php

namespace App\Request\Discount;

use App\Type\DiscountType;
use App\Validator\UserDiscount;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

#[UserDiscount]
class CreateUserDiscountRequest
{
    #[NotBlank]
    #[GreaterThan(0)]
    public float $discount;

    #[NotBlank]
    public DiscountType $type;
}