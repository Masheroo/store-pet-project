<?php

namespace App\Request;

use App\Security\AccessValue;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddAccessRightRequest
{
    #[NotBlank]
    public ?AccessValue $accessValue = null;
}