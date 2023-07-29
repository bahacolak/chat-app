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
        if (empty($data['name'])) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group name is required.']));
            return $errorResponse;
        }


        $name = $data['name'];

        $this->groupModel->createGroup($name);
        //There can be multiple groups with the same name.

        $responseArray = ['message' => 'New group has been successfully created.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function addMessage(Request $request, Response $response): ResponseInterface
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


    //validation not working well 
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

    //validation not working well
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

        $messages = $this->messageModel->getMessagesByGroupAndUser($groupId, $userId);

        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function joinGroup(Request $request, Response $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        $groupId = $data['group_id'];
        $userId = $data['user_id'];

        if (empty($groupId) || empty($userId)) {
            $errorResponse = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Invalid group_id or user_id.']));
            return $errorResponse;
        }

        if (!$this->groupModel->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        if ($this->groupModel->isUserJoined($groupId, $userId)) {
            $errorResponse = $response->withStatus(409)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User already joined the group.']));
            return $errorResponse;
        }

        $this->groupModel->joinGroup($groupId, $userId);

        $responseArray = ['message' => 'User successfully joined the group.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
