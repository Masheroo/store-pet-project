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
use function PHPUnit\Framework\assertArrayHasKey;

final class JsonExceptionListenerTest extends TestCase
{
    private EventDispatcher $eventDispatcher;
    private JsonExceptionListener $listener;

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
        $kernel = new Kernel('test', true);
        $request = Request::createFromGlobals();
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->eventDispatcher->dispatch($event, KernelEvents::EXCEPTION);
        self::assertInstanceOf(JsonResponse::class, $event->getResponse());
    }

    #[DataProvider('provideExceptions')]
    public function testResponseBody(Throwable $exception){
        $kernel = new Kernel('test', true);
        $request = Request::create(uri: '', parameters: ['email' => 'example@mail.ru', 'password' => 'example']);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->eventDispatcher->dispatch($event, KernelEvents::EXCEPTION);
        $responseContent = json_decode($event->getResponse()->getContent(), true);

        assertArrayHasKey('error', $responseContent);
        assertArrayHasKey('message', $responseContent['error']);
        assertArrayHasKey('code', $responseContent['error']);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->listener = new JsonExceptionListener();
        $this->eventDispatcher->addListener(KernelEvents::EXCEPTION, [$this->listener, '__invoke']);
        parent::setUp();
    }
}