<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface as Response;

class ResponseService
{
    public static function sendSuccess(Response $response, $data = []): Response
    {
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public static function sendError(Response $response, int $statusCode, string $message): Response
    {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}
