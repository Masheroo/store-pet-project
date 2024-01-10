<?php

namespace App\Request\Discount;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateVolumeDiscountRequest extends CreateDiscountRequest
{
    #[NotBlank]
    #[GreaterThan(0)]
    public int $amount;

}