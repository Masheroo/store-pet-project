<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function deleteAllByByUser(UserInterface $user): void
    {
        $this->createQueryBuilder('token')
            ->delete()
            ->where('token.username = :username')
            ->setParameter('username', $user->getUserIdentifier())
            ->getQuery()
            ->execute();
    }
}
