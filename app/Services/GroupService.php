<?php


namespace App\Services;

use App\Models\GroupModel;

class GroupService
{
    protected $groupModel;

    public function __construct(GroupModel $groupModel)
    {
        $this->groupModel = $groupModel;
    }

    public function getAllGroups()
    {
        return $this->groupModel->getAllGroups();
    }

    public function createGroup($name)
    {
        return $this->groupModel->createGroup($name);
    }

    public function groupExists($groupId)
    {
        return $this->groupModel->groupExists($groupId);
    }

    public function joinGroup($groupId, $userId)
    {
        return $this->groupModel->joinGroup($groupId, $userId);
    }

    public function isUserJoined($groupId, $userId)
    {
        return $this->groupModel->isUserJoined($groupId, $userId);
    }

    public function isUserInGroup($groupId, $userId)
    {
        return $this->groupModel->isUserInGroup($groupId, $userId);
    }

    public function isUserMessageInGroup($groupId, $userId)
    {
        return $this->groupModel->isUserMessageInGroup($groupId, $userId);
    }
}


