<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\GroupModel;
use App\Models\MessageModel;
use Psr\Http\Message\ResponseInterface;


class ApiController
{
    protected $groupModel;
    protected $messageModel;

    public function __construct(GroupModel $groupModel, MessageModel $messageModel)
    {
        $this->groupModel = $groupModel;
        $this->messageModel = $messageModel;
    }

    public function getGroups(Request $request, Response $response): Response
    {
        $groups = $this->groupModel->getAllGroups();
        $response->getBody()->write(json_encode($groups));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createGroup(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $name = $data['name'];

        $this->groupModel->createGroup($name);

        $responseArray = ['message' => 'New group successfully created.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function addMessage(Request $request, Response $response): ResponseInterface
    {
        try {
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
    
            $this->messageModel->addMessage($groupId, $userId, $content);
    
            $responseArray = ['message' => 'New message successfully added.'];
            $response->getBody()->write(json_encode($responseArray));
            return $response->withHeader('Content-Type', 'application/json');
    
        } catch (\PDOException $e) {
            error_log($e->getMessage());
    
            $errorResponse = $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'An error occurred while adding the message.']));
            return $errorResponse;
        }
    }


    public function getMessagesByGroup(Request $request, Response $response, $args): Response
    {
        $groupId = $args['group_id'];

        $messages = $this->messageModel->getMessagesByGroup($groupId);
        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function joinGroup(Request $request, Response $response): ResponseInterface
{
    try {
        $data = $request->getParsedBody();

        $groupId = $data['group_id'];
        $userId = $data['user_id'];

        // Check if 'group_id' and 'user_id' keys exist and are not empty
        if (empty($groupId) || empty($userId)) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Invalid group_id or user_id.']));
            return $errorResponse;
        }

        // Check if the group exists
        if (!$this->groupModel->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        // Check if the user is already joined the group
        if ($this->groupModel->isUserJoined($groupId, $userId)) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User already joined the group.']));
            return $errorResponse;
        }

        // Grup üyeleri tablosuna yeni katılımı ekleyelim.
        $this->groupModel->joinGroup($groupId, $userId);

        $responseArray = ['message' => 'User successfully joined the group.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');

    } catch (\PDOException $e) {
        error_log($e->getMessage());
        $errorResponse = $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
        $errorResponse->getBody()->write(json_encode(['error' => 'An error occurred while saving to the database.']));
        return $errorResponse;
    }
}

}

