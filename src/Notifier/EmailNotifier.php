<?php

namespace App\Notifier;

use App\Entity\Lot;
use App\Service\Manager\LocalImageManager;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifier
{
    public function __construct(
        private readonly string $recipientEmail,
        private readonly MailerInterface $notifier,
        private readonly LocalImageManager $imageManager,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailAboutNewLot(Lot $lot, string $userEmail): void
    {
        $template = sprintf('
            <img src="%s">
            <p>Номер лота: %s</p>
            <p>Название лота: %s</p>
            <p>Цена за единицу: %s</p>
            <p>Количество: %s</p>
        ', $this->imageManager->getPublicLink($lot->getImage()), $lot->getId(), $lot->getTitle(), $lot->getCost(), $lot->getCount());

        $notification = (new Email())
            ->from($this->recipientEmail)
            ->to($userEmail)
            ->subject('Новый лот!')
            ->html($template);

        $this->notifier->send($notification);
    }
}
