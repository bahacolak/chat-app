<?php
namespace App\Models;

class GroupModel
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function groupExists($groupId)
{
    $sql = 'SELECT COUNT(*) FROM groups WHERE id = :group_id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':group_id', $groupId);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
}


    public function getAllGroups()
    {
        $stmt = $this->pdo->query('SELECT * FROM groups');
        return $stmt->fetchAll();
    }

    public function createGroup($name)
    {
        $sql = 'INSERT INTO groups (name) VALUES (:name)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }
}
