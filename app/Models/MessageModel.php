<?php

namespace App\Models;

class MessageModel
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addMessage($groupId, $userId, $content)
    {
        try {
            $sql = 'INSERT INTO messages (group_id, user_id, content) VALUES (:group_id, :user_id, :content)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':content', $content);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getMessagesByGroup($groupId)
    {
        $sql = 'SELECT * FROM messages WHERE group_id = :group_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMessagesByGroupAndUser($groupId, $userId)
    {
        $sql = 'SELECT * FROM messages WHERE group_id = :group_id AND user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
