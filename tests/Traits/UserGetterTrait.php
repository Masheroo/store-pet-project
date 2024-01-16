<?php

namespace App\Tests\Traits;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait UserGetterTrait
{
    public function getUserByEmail(string $email, ContainerInterface $container): User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        return $userRepository->findOneBy(['email' => $email]) ?? throw new \DomainException('User not found');
    }
}