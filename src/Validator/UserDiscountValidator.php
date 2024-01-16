<?php

namespace App\Validator;

use App\Request\Discount\CreateUserDiscountRequest;
use App\Type\DiscountType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UserDiscountValidator extends ConstraintValidator
{
    /** @param CreateUserDiscountRequest $value */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof CreateUserDiscountRequest) {
            throw new UnexpectedValueException($value, CreateUserDiscountRequest::class);
        }

        if (!$constraint instanceof UserDiscount) {
            throw new UnexpectedValueException($constraint, UserDiscount::class);
        }

        if (!isset($value->type) || !isset($value->discount)) {
            return;
        }

        if (DiscountType::Percent != $value->type) {
            return;
        }

        if ($value->discount > 1 || $value->discount < 0) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('discount')
                ->addViolation();
        }
    }
}
