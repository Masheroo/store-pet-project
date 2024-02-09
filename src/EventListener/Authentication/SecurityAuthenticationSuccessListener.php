<?php

namespace App\EventListener\Authentication;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(AuthenticationSuccessEvent::class)]
class SecurityAuthenticationSuccessListener
{
    public function __construct(
        private readonly JWTTokenManagerInterface $JWTTokenManager,
        private readonly SerializerInterface $serializer
    )
    {
    }

    /**
     * @throws JWTDecodeFailureException
     */
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $token = $event->getAuthenticationToken();

        if ($token instanceof JWTPostAuthenticationToken){
            $payload = $this->JWTTokenManager->decode($token);

            /** @var User $user */
            $user = $token->getUser();
            if ($user->getLastLogoutAt()?->getTimestamp() != $payload['lastLogoutAt'])
            {
                throw new InvalidTokenException();
            }
        }
    }
}