<?php

namespace App\Request;

use App\Entity\City;
use App\Entity\User;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationRequest
{
    #[Email]
    #[NotBlank]
    public string $email;

    #[NotBlank]
    #[EntityExist(entity: City::class)]
    public int $city;

    #[NotBlank]
    #[Length(min: User::PASSWORD_MIN_LENGTH, minMessage: 'This password is too short. It should have 6 characters or more')]
    public string $password = '';
}
