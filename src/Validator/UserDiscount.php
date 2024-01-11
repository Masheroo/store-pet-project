<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UserDiscount extends Constraint
{
    public string $message = 'If discount type is "percent", it must be in range 0 to 1';
    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}