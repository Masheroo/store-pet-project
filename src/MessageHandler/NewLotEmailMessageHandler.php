<?php

namespace App\MessageHandler;

use App\Message\NewLotEmailMessage;
use App\Notifier\EmailNotifier;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewLotEmailMessageHandler
{
    public function __construct(
        private readonly EmailNotifier $emailNotifier
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(NewLotEmailMessage $message): void
    {
        $this->emailNotifier->sendEmailAboutNewLot($message);
    }
}
