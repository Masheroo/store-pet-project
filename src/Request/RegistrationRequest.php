<?php

namespace App\Request;

use App\Entity\User;
use App\Validator\ExistsCity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationRequest
{
    #[Email]
    #[NotBlank]
    public string $email;

    #[NotBlank]
    #[ExistsCity]
    public int $city;

    #[NotBlank]
    #[Length(min: User::PASSWORD_MIN_LENGTH, minMessage: 'This password is too short. It should have 6 characters or more')]
    public string $password = '';
}
