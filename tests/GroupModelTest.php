<?php

use PHPUnit\Framework\TestCase;
use App\Models\GroupModel;

class GroupModelTest extends TestCase
{
    protected $pdo;
    protected $groupModel;

    protected function setUp(): void
    {
        $dsn = 'sqlite::memory:';
        $this->pdo = new PDO($dsn);
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS groups (id INTEGER PRIMARY KEY, name TEXT)');

        $this->groupModel = new GroupModel($this->pdo);
    }

    public function testCreateGroup()
    {
        $name = 'Test Group';

        $this->assertTrue($this->groupModel->createGroup($name));
        $this->assertTrue($this->groupModel->createGroup($name));
    }

    public function testGroupExists()
    {
        $name = 'Existing Group';

        $this->groupModel->createGroup($name);
        $this->assertTrue($this->groupModel->groupExists(1));
        $this->assertFalse($this->groupModel->groupExists(999));
    }
}