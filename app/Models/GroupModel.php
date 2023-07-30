<?php

namespace App\Models;

class GroupModel
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createGroupTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS group_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            FOREIGN KEY (group_id) REFERENCES groups (id),
            FOREIGN KEY (user_id) REFERENCES users (id)
        )';
        $this->pdo->exec($sql);
    }

    public function groupExists($groupId)
    {
        $sql = 'SELECT * FROM groups WHERE id = :group_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result !== false;
    }


    public function isUserInGroup($groupId, $userId)
    {
        $sql = 'SELECT * FROM group_users WHERE group_id = :group_id AND user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result !== false;
    }

    public function isUserMessageInGroup($groupId, $userId)
    {
        $sql = 'SELECT * FROM messages WHERE group_id = :group_id AND user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result !== false;
    }



    public function isUserJoined($groupId, $userId)
    {
        $sql = 'SELECT COUNT(*) FROM group_users WHERE group_id = :group_id AND user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function joinGroup($groupId, $userId)
    {
        try {
            $sql = 'INSERT INTO group_users (group_id, user_id) VALUES (:group_id, :user_id)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    public function getAllGroups()
    {
        $stmt = $this->pdo->query('SELECT * FROM groups');
        return $stmt->fetchAll();
    }

    public function createGroup($name)
    {
        try {
            $sql = 'INSERT INTO groups (name) VALUES (:name)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
