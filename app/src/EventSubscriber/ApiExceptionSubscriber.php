<?php

namespace App\EventSubscriber;

use App\Api\ApiProblem;
use App\Exception\ApiProblemException;
use App\Factory\ApiResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        if ($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } else {
            $apiProblem = new ApiProblem($statusCode);
            if ($e instanceof HttpExceptionInterface) {
                $apiProblem->set('detail', $e->getMessage());
            }
        }
        $response = ApiResponseFactory::createResponse($apiProblem);
        $event->setResponse($response);
    }


}
