<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\MessageService;
use App\Services\GroupService;
use App\Services\ResponseService;

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
            return ResponseService::sendError($response, 400, 'Content field is required.'); // ResponseService'yi kullandık
        }

        $groupId = $args['group_id'];
        $userId = $args['user_id'];
        $content = $data['content'];

        if (!$this->groupService->groupExists($groupId)) {
            return ResponseService::sendError($response, 404, 'Group not found.'); // ResponseService'yi kullandık
        }

        if (!$this->groupService->isUserInGroup($groupId, $userId)) {
            return ResponseService::sendError($response, 403, 'User is not a member of the group.'); // ResponseService'yi kullandık
        }

        $this->messageService->addMessage($groupId, $userId, $content);

        $responseArray = ['message' => 'New message has been successfully added.'];
        return ResponseService::sendSuccess($response, $responseArray);
    }

    public function getMessagesByGroup(Request $request, Response $response, array $args): Response
    {
        $groupId = $args['group_id'];

        if (!$this->groupService->groupExists($groupId)) {
            return ResponseService::sendError($response, 404, 'Group not found.');
        }

        $messages = $this->messageService->getMessagesByGroup($groupId);

        return ResponseService::sendSuccess($response, $messages);
    }

    public function getMessagesByGroupAndUser(Request $request, Response $response, array $args): Response
    {
        $groupId = $args['group_id'];
        $userId = $args['user_id'];


        if (!$this->groupService->groupExists($groupId)) {
            return ResponseService::sendError($response, 404, 'Group not found.');
        }

        if (!$this->groupService->isUserMessageInGroup($groupId, $userId)) {
            return ResponseService::sendError($response, 404, 'User messages not found in the group.'); 
        }

        $messages = $this->messageService->getMessagesByGroupAndUser($groupId, $userId);

        return ResponseService::sendSuccess($response, $messages);
    }
}
