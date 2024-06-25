<?php

namespace App\Repository;

use App\Entity\ExternalApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExternalApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalApiToken::class);
    }

    public function findByToken(string $token): ?ExternalApiToken
    {
        return $this->findOneBy(['token' => $token]);
    }
}
