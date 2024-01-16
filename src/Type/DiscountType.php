<?php

namespace App\Type;

enum DiscountType: int
{
    case Percent = 0;
    case Absolute = 1;
}