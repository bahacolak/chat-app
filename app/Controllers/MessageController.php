<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\MessageService;
use App\Services\GroupService;

class MessageController
{
    protected $messageService;
    protected $groupService;

    public function __construct(MessageService $messageService, GroupService $groupService)
    {
        $this->messageService = $messageService;
        $this->groupService = $groupService;
    }

    public function addMessage(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        if (empty($data['content'])) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Content field is required.']));
            return $errorResponse;
        }

        $groupId = $args['group_id'];
        $userId = $args['user_id'];
        $content = $data['content'];

        if (!$this->groupService->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        if (!$this->groupService->isUserInGroup($groupId, $userId)) {
            $errorResponse = $response->withStatus(403) // 403 Forbidden
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User is not a member of the group.']));
            return $errorResponse;
        }

        $this->messageService->addMessage($groupId, $userId, $content);

        $responseArray = ['message' => 'New message has been successfully added.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMessagesByGroup(Request $request, Response $response, array $args): Response
    {
        $groupId = $args['group_id'];

        if (!$this->groupService->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        $messages = $this->messageService->getMessagesByGroup($groupId);

        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMessagesByGroupAndUser(Request $request, Response $response, array $args): Response
    {
        $groupId = $args['group_id'];
        $userId = $args['user_id'];


        if (!$this->groupService->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        if (!$this->groupService->isUserMessageInGroup($groupId, $userId)) {
            $errorResponse = $response->withStatus(404) // 404 Not Found
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User messages not found in the group.']));
            return $errorResponse;
        }

        $messages = $this->messageService->getMessagesByGroupAndUser($groupId, $userId);

        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
