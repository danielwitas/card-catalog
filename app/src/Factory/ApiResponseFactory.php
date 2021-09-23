<?php

namespace App\Factory;

use App\Api\ApiProblem;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseFactory
{
    public static function createResponse(ApiProblem $apiProblem): JsonResponse
    {
        $data = $apiProblem->toArray();
        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');
        return $response;
    }
}