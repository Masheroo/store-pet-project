<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class BuyLotRequest
{
    #[NotBlank]
    #[GreaterThan(0)]
    public int $quantity;
}