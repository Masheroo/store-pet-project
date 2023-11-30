<?php

namespace App\EventListener\ExceptionListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Throwable;

#[AsEventListener(KernelEvents::EXCEPTION)]
class JsonExceptionListener
{
    private static array $exceptions = [
        JsonException::class,
        NotEncodableValueException::class
    ];

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable()->getPrevious();

        if (!in_array($exception::class, self::$exceptions)) {
            return;
        }


        $response = (new JsonResponse())
            ->setContent($this->exceptionToJson($exception))
            ->setStatusCode(Response::HTTP_BAD_REQUEST);

        $event->setResponse($response);
    }

    private function exceptionToJson(Throwable $exception): bool|string
    {
        return json_encode([
            'error' => [
                'message' => 'Unable to decode request',
                'code' => $exception->getCode()
            ]
        ]);
    }
}