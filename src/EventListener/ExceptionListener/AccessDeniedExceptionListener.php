<?php

namespace App\EventListener\ExceptionListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 2)]
class AccessDeniedExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $event->setResponse($this->transformExceptionToJsonResponse($exception));
    }

    private function transformExceptionToJsonResponse(AccessDeniedException $exception): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => $exception->getMessage(),
                'code' => $code = $exception->getCode(),
            ],
            $code
        );
    }
}
