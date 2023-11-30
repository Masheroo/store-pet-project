<?php

namespace App\Tests\EventListener\ExceptionEventListener;

use App\EventListener\ExceptionListener\JsonExceptionListener;
use App\Kernel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Throwable;

final class JsonExceptionListenerTest extends TestCase
{
    private EventDispatcher $eventDispatcher;

    public static function provideExceptions(): array
    {
        return [
            [
                new JsonException()
            ],
            [
                new NotEncodableValueException()
            ]
        ];
    }

    #[DataProvider('provideExceptions')]
    public function testOnExceptionEventDispatch(Throwable $exception): void
    {
        $listener = new JsonExceptionListener();
        $this->eventDispatcher->addListener(KernelEvents::EXCEPTION, [$listener, '__invoke']);

        $kernel = new Kernel('test', true);
        $request = Request::createFromGlobals();

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
        $this->eventDispatcher->dispatch($event, KernelEvents::EXCEPTION);
        self::assertInstanceOf(JsonResponse::class, $event->getResponse());
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
        parent::setUp();
    }
}