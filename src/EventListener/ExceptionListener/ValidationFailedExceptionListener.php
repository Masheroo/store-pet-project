<?php

namespace App\EventListener\ExceptionListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class ValidationFailedExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable()->getPrevious() ?? $event->getThrowable();

        if (!$exception instanceof ValidationFailedException) {
            return;
        }

        $response = (new JsonResponse())
            ->setContent($this->exceptionToJson($exception))
            ->setStatusCode(Response::HTTP_BAD_REQUEST);

        $event->setResponse($response);
    }

    private function exceptionToJson(ValidationFailedException $exception): bool|string
    {
        $messages = [];

        /** @var ConstraintViolation $violation */
        foreach ($exception->getViolations() as $violation) {
            $messages[] = [
                'message' => $violation->getMessage(),
                'property' => $violation->getPropertyPath(),
                'gotValue' => $violation->getInvalidValue(),
            ];
        }

        return json_encode([
            'errors' => $messages,
            'code' => $exception->getCode(),
        ]);
    }
}
