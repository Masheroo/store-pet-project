<?php

namespace App\EventListener\ExceptionListener;

use App\Exceptions\RoleDoesNotExistsException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class RoleDoesNotExistsExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof RoleDoesNotExistsException){
            return;
        }

        $event->setResponse(new JsonResponse([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ], $exception->getCode()));
    }
}