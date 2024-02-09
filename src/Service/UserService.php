<?php

namespace App\Service;

use App\Entity\City;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function createAdmin(string $email, string $password, ?City $city): User
    {
        $admin = $this->createUserWithoutRole($email, $password, $city);
        $admin->setRoles([User::ROLE_ADMIN]);

        return $admin;
    }

    public function createUser(?string $email, string $password, ?City $city): User
    {
        $user = $this->createUserWithoutRole($email, $password, $city);
        $user->setRoles([User::ROLE_USER]);

        return $user;
    }

    private function createUserWithoutRole(?string $email, string $password, ?City $city): User
    {
        $user = new User();

        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setCity($city);

        $violations = $this->validator->validate($user);

        if (0 != count($violations)) {
            throw new ValidationFailedException('Validation Failed', $violations);
        }

        return $user;
    }

    public function createManager(string $email, string $password, City $city): User
    {
        $user = $this->createUserWithoutRole($email, $password, $city);
        $user->setRoles([User::ROLE_MANAGER]);

        return $user;
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $violations = $this->validator->validate($newPassword, new Length(min:3));
        if (count($violations) != 0) {
            throw new ValidatorException('Password cannot be changed', $violations);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->entityManager->flush();
    }
}
