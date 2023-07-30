<?php

namespace App\Services;

use App\Models\MessageModel;

class MessageService
{
    protected $messageModel;

    public function __construct(MessageModel $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    public function addMessage($groupId, $userId, $content)
    {
        return $this->messageModel->addMessage($groupId, $userId, $content);
    }

    public function getMessagesByGroup($groupId)
    {
        return $this->messageModel->getMessagesByGroup($groupId);
    }

    public function getMessagesByGroupAndUser($groupId, $userId)
    {
        return $this->messageModel->getMessagesByGroupAndUser($groupId, $userId);
    }
}
