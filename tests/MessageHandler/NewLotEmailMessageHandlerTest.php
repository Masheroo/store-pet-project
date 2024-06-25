<?php

namespace App\Tests\MessageHandler;

use App\Message\NewLotEmailMessage;
use App\MessageHandler\NewLotEmailMessageHandler;
use App\Notifier\EmailNotifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class NewLotEmailMessageHandlerTest extends TestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testInvoke(): void
    {
        $emailNotifier = $this->getMockBuilder(EmailNotifier::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = new NewLotEmailMessageHandler($emailNotifier);
        $emailNotifier->expects(self::exactly(1))->method('sendEmailAboutNewLot');

        $message = new NewLotEmailMessage(0, 'title', 1, 1, 'image', 'email');
        $handler($message);
    }
}
