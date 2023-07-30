<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\GroupService;

class GroupController
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function getAllGroups(Request $request, Response $response): Response
    {
        $groups = $this->groupService->getAllGroups();
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
        //Multiple groups can exist with the same name.

        $this->groupService->createGroup($name);

        $responseArray = ['message' => 'New group has been successfully created.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function joinGroup(Request $request, Response $response): Response
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

        if (!$this->groupService->groupExists($groupId)) {
            $errorResponse = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'Group not found.']));
            return $errorResponse;
        }

        if ($this->groupService->isUserJoined($groupId, $userId)) {
            $errorResponse = $response->withStatus(409)
                ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write(json_encode(['error' => 'User already joined the group.']));
            return $errorResponse;
        }

        $this->groupService->joinGroup($groupId, $userId);

        $responseArray = ['message' => 'User successfully joined the group.'];
        $response->getBody()->write(json_encode($responseArray));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
