<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Factory\AppFactory;
use App\Controllers\GroupController;
use App\Services\GroupService;
use App\Models\GroupModel;

class GroupLoginTest extends TestCase
{
    protected static $pdo;
    protected $app;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$pdo->exec("
            CREATE TABLE groups (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL
            );

            CREATE TABLE users (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL
            );

            CREATE TABLE group_users (
                group_id INTEGER,
                user_id INTEGER,
                FOREIGN KEY (group_id) REFERENCES groups(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            );
        ");

        self::$pdo->exec("
            INSERT INTO groups (name) VALUES ('Group 1');
            INSERT INTO groups (name) VALUES ('Group 2');

            INSERT INTO users (name) VALUES ('User 1');
            INSERT INTO users (name) VALUES ('User 2');
        ");
    }

    public function setUp(): void
    {
        $groupModel = new GroupModel(self::$pdo);
        $groupService = new GroupService($groupModel);

        $controller = new GroupController($groupService);

        $this->app = AppFactory::create();

        $this->app->post('/groups/join', function (Request $request, Response $response) use ($controller) {
            return $controller->joinGroup($request, $response);
        });
    }

    public function testJoinGroup()
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', '/groups/join')
            ->withParsedBody(['group_id' => 1, 'user_id' => 1]);

        $response = new SlimResponse();

        $response = $this->app->handle($request);

        $expectedResponse = ['success' => true, 'data' => ['message' => 'User successfully joined the group.']];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), (string) $response->getBody());
    }
}
