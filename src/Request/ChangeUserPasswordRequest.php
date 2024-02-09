<?php

namespace App\Request;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeUserPasswordRequest
{
    #[NotBlank]
    #[Length(min: User::PASSWORD_MIN_LENGTH, minMessage: 'This password is too short. It should have 6 characters or more')]
    public string $password = '';
}