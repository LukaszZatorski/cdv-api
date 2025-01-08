<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonRequestValidatorListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if (!in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return;
        }

        if ($request->headers->get('Content-Type') !== 'application/json') {
            $event->setResponse(new JsonResponse(['error' => 'Invalid content type'], JsonResponse::HTTP_BAD_REQUEST));
            return;
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            $event->setResponse(new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST));
            return;
        }
    }
}