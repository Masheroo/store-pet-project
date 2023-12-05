<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
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
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function createAdmin(string $email, string $password): void
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

        $admin = new User();

        $admin->setEmail($email);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $admin->setRoles([$admin::ROLE_ADMIN]);

        $this->userRepository->persistAndFlush($admin);
    }
}
