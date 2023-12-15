<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function createAdmin(?string $email, string $password): User
    {
        $admin = $this->createUserWithoutRole($email, $password);
        $admin->setRoles([User::ROLE_ADMIN]);

        return $admin;
    }

    public function createUser(?string $email, string $password): User
    {
        $user = $this->createUserWithoutRole($email, $password);
        $user->setRoles([User::ROLE_USER]);

        return $user;
    }

    private function createUserWithoutRole(?string $email, string $password): User
    {
        $user = new User();

        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $violations = $this->validator->validate($user);

        if (0 != count($violations)) {
            throw new ValidationFailedException('Validation Failed', $violations);
        }

        return $user;
    }

    public function createManager(string $email, string $password): User
    {
        $user = $this->createUserWithoutRole($email, $password);
        $user->setRoles([User::ROLE_MANAGER]);

        return $user;
    }
}
