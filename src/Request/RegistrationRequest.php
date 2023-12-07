<?php

namespace App\Request;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationRequest
{
    #[Email]
    #[NotBlank]
    public ?string $email = null;
    #[NotBlank]
    #[Length(min: User::PASSWORD_MIN_LENGTH, minMessage: 'This password is too short. It should have 6 characters or more')]
    public string $password = '';

    public function __construct()
    {
    }
}
