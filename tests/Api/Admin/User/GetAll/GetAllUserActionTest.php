<?php

namespace UnitTesting\Api\Admin\User\GetAll;

use Nebalus\Webapi\Api\Admin\User\GetAll\GetAllUserAction;
use Nebalus\Webapi\Api\Admin\User\GetAll\GetAllUserService;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class GetAllUserActionTest extends TestCase
{
    private GetAllUserService $service;
    private GetAllUserAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(GetAllUserService::class);
        $this->action = new GetAllUserAction($this->service);
    }

    #[Test]
    public function testInvokeExecutesServiceAndReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = [];
        $userPerms = $this->createMock(UserPermissionIndex::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(AttributeTypes::CLIENT_USER_PERMISSION_INDEX)
            ->willReturn($userPerms);

        $result = $this->createMock(ResultInterface::class);
        $result->method('getPayload')->willReturn(['data' => 'test']);
        $result->method('getStatusCode')->willReturn(200);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($userPerms)
            ->willReturn($result);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['data' => 'test'], 200)
            ->willReturnSelf();

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
