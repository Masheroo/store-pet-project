<?php

namespace App\Notifier;

use App\Message\NewLotEmailMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifier
{
    public function __construct(
        private readonly string $recipientEmail,
        private readonly MailerInterface $notifier,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailAboutNewLot(NewLotEmailMessage $message): void
    {
        $template = sprintf('
            <img src="%s">
            <p>Номер лота: %s</p>
            <p>Название лота: %s</p>
            <p>Цена за единицу: %s</p>
            <p>Количество: %s</p>
        ', $message->getLotImageUrl() ?? '',
            $message->getLotId() ?? '',
            $message->getLotTitle() ?? '',
            $message->getLotCost() ?? '',
            $message->getLotCount()) ?? '';

        $notification = (new Email())
            ->from($this->recipientEmail)
            ->to($message->getUserEmail())
            ->subject('Новый лот!')
            ->html($template);

        $this->notifier->send($notification);
    }
}
