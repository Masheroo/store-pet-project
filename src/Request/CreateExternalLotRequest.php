<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateExternalLotRequest
{
    #[NotBlank]
    #[Length(min: 3)]
    public string $token_name;
}