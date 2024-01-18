<?php

namespace App\Tests\Notifier;

use App\Message\NewLotEmailMessage;
use App\Notifier\EmailNotifier;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailNotifierTest extends WebTestCase
{
    use MailerAssertionsTrait;
    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function testSendEmailAboutNewLotCalledSendMethod(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $emailNotifier = new EmailNotifier('test@mail.ru', $mailer);

        $message = new NewLotEmailMessage(0, 'title', 1, 10, 'image', 'someEmail@mail.ru');

        $mailer->expects(self::once())->method('send');
        $emailNotifier->sendEmailAboutNewLot($message);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testSendEmailAboutNewLotEmailSend(): void
    {
        $container = self::getContainer();
        /** @var EmailNotifier $emailNotifier */
        $emailNotifier = $container->get(EmailNotifier::class);

        $message = new NewLotEmailMessage(1, 'Test', 1, 10, 'image', 'test@mail.ru');

        $emailNotifier->sendEmailAboutNewLot($message);

        self::assertEmailCount(1);

        $email = self::getMailerMessage();
        assert($email != null);

        self::assertEmailHtmlBodyContains($email, 'Test');
        self::assertEmailHtmlBodyContains($email, 'image');
        self::assertEmailAddressContains($email, 'to', 'test@mail.ru');
    }
}
