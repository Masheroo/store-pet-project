<?php

namespace App\Service\ExternalApiToken;

use App\Entity\ExternalApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;

class ExternalApiTokenService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )  {
    }

    public function create(string $name): ExternalApiToken
    {
        $token = new ExternalApiToken(ByteString::fromRandom(64), $name);
        $this->entityManager->persist($token);
        $this->entityManager->flush();
        return $token;
    }
}