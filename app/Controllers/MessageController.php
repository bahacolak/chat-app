<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\MessageModel;
use App\Models\GroupModel;

class MessageController
{
    protected $messageModel;
    protected $groupModel;

    public function __construct(MessageModel $messageModel, GroupModel $groupModel)
    {
        $this->messageModel = $messageModel;
        $this->groupModel = $groupModel;
    }

    public function addMessage(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (empty($data['group_id']) || empty($data['user_id']) || empty($data['content'])) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'group_id, user_id, and content fields are required.']));
            return $errorResponse;
        }

        $groupId = $data['group_id'];
        $userId = $data['user_id'];
        $content = $data['content'];

        if (!$this->groupModel->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        if (!$this->groupModel->isUserInGroup($groupId, $userId)) {
            $errorResponse = $response->withStatus(403) // 403 Forbidden
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User is not a member of the group.']));
            return $errorResponse;
        }

        $this->messageModel->addMessage($groupId, $userId, $content);

        $responseArray = ['message' => 'New message has been successfully added.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMessagesByGroup(Request $request, Response $response, array $args): Response
    {
        $groupId = $args['group_id'];

        if (empty($groupId)) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'group_id is required.']));
            return $errorResponse;
        }

        if (!$this->groupModel->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        $messages = $this->messageModel->getMessagesByGroup($groupId);

        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMessagesByGroupAndUser(Request $request, Response $response, array $args): Response
    {
        $groupId = $args['group_id'];
        $userId = $args['user_id'];

        if (empty($groupId) || empty($userId)) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'group_id and user_id are required.']));
            return $errorResponse;
        }

        if (!$this->groupModel->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        if (!$this->groupModel->isUserMessageInGroup($groupId, $userId)) {
            $errorResponse = $response->withStatus(404)//HATA KODUNA BAK
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User messages not found in the group.']));
            return $errorResponse;
        }

        $messages = $this->messageModel->getMessagesByGroupAndUser($groupId, $userId);

        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }
}