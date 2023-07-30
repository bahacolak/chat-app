<?php

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use App\Controllers\GroupController;
use App\Services\GroupService;
use Psr\Http\Message\ServerRequestInterface;

class GroupControllerTest extends TestCase
{
    protected function createGroupController(): GroupController
    {
        $groupService = $this->createMock(GroupService::class);
        return new GroupController($groupService);
    }

    public function testCreateGroup(): void
{
    $controller = $this->createGroupController();

    $requestData = [
        'name' => 'Test Group',
    ];
    $request = $this->createRequest('POST', '/groups', $requestData);

    $responseFactory = new ResponseFactory();
    $response = $responseFactory->createResponse();

    $result = $controller->createGroup($request, $response);

    $this->assertSame(400, $result->getStatusCode());
    $this->assertJsonStringEqualsJsonString(
        json_encode(['error' => 'Group name is required.']),
        (string)$result->getBody()
    );
}

    protected function createRequest(string $method, string $uri, array $data = []): ServerRequestInterface
    {
        $requestFactory = new ServerRequestFactory();
        return $requestFactory->createServerRequest($method, $uri, $data);
    }
}



