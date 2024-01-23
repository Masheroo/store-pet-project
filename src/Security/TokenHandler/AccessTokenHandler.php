<?php

namespace App\Security\TokenHandler;

use App\Repository\ExternalApiTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly ExternalApiTokenRepository $apiTokenRepository
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $accessToken = $this->apiTokenRepository->findByToken($accessToken) ?? throw new InvalidTokenException('Invalid external token');

        return new UserBadge($accessToken->getUserIdentifier());
    }
}
