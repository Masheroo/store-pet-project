<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\NotBlank;

class AddCategoryFieldRequest
{
    #[NotBlank]
    public string $name;
}