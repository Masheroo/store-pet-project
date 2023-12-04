<?php

namespace App\Tests\EventListener\ExceptionEventListener;

use App\EventListener\ExceptionListener\ValidationFailedExceptionListener;
use App\Kernel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

use Throwable;

use function PHPUnit\Framework\assertArrayHasKey;

final class ValidationFailedExceptionListenerTest extends TestCase
{
    public const PROPERTY_ERROR_MESSAGE = 'property error message';
    public const PROPERTY_ERROR_VALUE = 'property error value';
    public const PROPERTY_ERROR_PATH = 'property path';
    private EventDispatcher $eventDispatcher;
    private ValidationFailedExceptionListener $listener;

    public static function provideExceptions(): array
    {
        return [
            [
                new ValidationFailedException(
                    'example.ru',
                    new ConstraintViolationList(
                        [
                            new ConstraintViolation(
                                self::PROPERTY_ERROR_MESSAGE,
                                null,
                                [],
                                0,
                                self::PROPERTY_ERROR_PATH,
                                self::PROPERTY_ERROR_VALUE
                            ),
                        ]
                    )
                ),
            ],
        ];
    }

    #[DataProvider('provideExceptions')]
    public function testOnExceptionEventDispatch(Throwable $exception): void
    {
        $kernel = new Kernel('test', true);
        $request = Request::createFromGlobals();
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->eventDispatcher->dispatch($event, KernelEvents::EXCEPTION);
        self::assertInstanceOf(JsonResponse::class, $event->getResponse());
    }

    #[DataProvider('provideExceptions')]
    public function testResponseBody(Throwable $exception)
    {
        $kernel = new Kernel('test', true);
        $request = Request::create(uri: '', parameters: ['email' => 'example@mail.ru', 'password' => 'example']);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->eventDispatcher->dispatch($event, KernelEvents::EXCEPTION);
        $responseContent = json_decode($event->getResponse()->getContent(), true);

        assertArrayHasKey('errors', $responseContent);
        assertArrayHasKey('code', $responseContent);
        assertArrayHasKey('message', $responseContent['errors'][0]);
        assertArrayHasKey('property', $responseContent['errors'][0]);
        assertArrayHasKey('gotValue', $responseContent['errors'][0]);

        self::assertEquals(self::PROPERTY_ERROR_PATH, $responseContent['errors'][0]['property']);
        self::assertEquals(self::PROPERTY_ERROR_VALUE, $responseContent['errors'][0]['gotValue']);
        self::assertEquals(self::PROPERTY_ERROR_MESSAGE, $responseContent['errors'][0]['message']);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->listener = new ValidationFailedExceptionListener();
        $this->eventDispatcher->addListener(KernelEvents::EXCEPTION, [$this->listener, '__invoke']);
        parent::setUp();
    }
}
