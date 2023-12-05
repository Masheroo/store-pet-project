<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function createAdmin(string $email, string $password): User
    {
        $admin = $this->createUserWithoutRole($email, $password);
        $admin->setRoles([User::ROLE_ADMIN]);

        return $admin;
    }

    public function createUser(string $email, string $password): User
    {
        $user = $this->createUserWithoutRole($email, $password);
        $user->setRoles([User::ROLE_USER]);

        return $user;
    }

    private function createUserWithoutRole(string $email, string $password): User
    {
        $violations = $this->validator->validate(
            [
                'email' => $email,
                'password' => $password,
            ],
            [
                new Collection([
                    'email' => [
                        new Email(),
                        new NotBlank(),
                    ],
                    'password' => [
                        new NotBlank(),
                        new Length(min: 6, max: 15),
                    ],
                ]),
            ]
        );

        if (0 != count($violations)) {
            throw new ValidationFailedException('Validation Failed', $violations);
        }

        $user = new User();

        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        return $user;
    }
}
