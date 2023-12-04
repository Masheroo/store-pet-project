<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(Events::AUTHENTICATION_SUCCESS)]
class AuthenticationSuccessEventListener
{
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        /** @var string[] $data */
        $data = $event->getData();

        $data['user_identity'] = $event->getUser()->getUserIdentifier();
        $data['access_token'] = $data['token'];
        unset($data['token']);

        $event->setData($data);
    }
}
