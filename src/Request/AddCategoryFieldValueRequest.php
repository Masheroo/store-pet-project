<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\NotBlank;

class AddCategoryFieldValueRequest
{
    #[NotBlank]
    public mixed $value;
}