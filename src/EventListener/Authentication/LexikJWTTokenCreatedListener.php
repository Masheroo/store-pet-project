<?php

namespace App\EventListener\Authentication;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(Events::JWT_CREATED)]
class LexikJWTTokenCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $userLastLogout = $user->getLastLogoutAt()?->getTimestamp();

        $payload = $event->getData();
        $payload['lastLogoutAt'] = $userLastLogout;
        $event->setData($payload);
    }
}