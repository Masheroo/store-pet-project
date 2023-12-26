<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReplenishRequest
{
    #[NotBlank]
    #[GreaterThan(0)]
    public ?float $amount = null;
}