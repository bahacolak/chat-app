<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\GroupService;
use App\Services\ResponseService;

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
        return ResponseService::sendSuccess($response, $groups);
    }

    public function createGroup(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        if (empty($data['name'])) {
            return ResponseService::sendError($response, 400, 'Group name is required.');
        }

        $name = $data['name'];
        //Multiple groups can exist with the same name.

        $this->groupService->createGroup($name);

        return ResponseService::sendSuccess($response, ['message' => 'New group has been successfully created.']);
    }

    public function joinGroup(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $groupId = $data['group_id'];
        $userId = $data['user_id'];

        if (empty($groupId) || empty($userId)) {
            return ResponseService::sendError($response, 400, 'Invalid group_id or user_id.');
        }

        if (!$this->groupService->groupExists($groupId)) {
            return ResponseService::sendError($response, 404, 'Group not found.');
        }

        if ($this->groupService->isUserJoined($groupId, $userId)) {
            return ResponseService::sendError($response, 409, 'User already joined the group.');
        }

        $this->groupService->joinGroup($groupId, $userId);

        return ResponseService::sendSuccess($response, ['message' => 'User successfully joined the group.']);
    }
}
