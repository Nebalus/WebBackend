<?php

namespace UnitTesting\Api\Admin\Permission\Get;

use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionAction;
use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionService;
use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class GetPermissionActionTest extends TestCase
{
    private GetPermissionService $service;
    private GetPermissionValidator $validator;
    private GetPermissionAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(GetPermissionService::class);
        $this->validator = $this->createMock(GetPermissionValidator::class);
        $this->action = new GetPermissionAction($this->service, $this->validator);
    }

    public function testInvokeExecutesServiceAndReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = ['permission_id' => 123];
        $userPerms = $this->createMock(UserPermissionIndex::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(AttributeTypes::CLIENT_USER_PERMISSION_INDEX)
            ->willReturn($userPerms);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $result = $this->createMock(ResultInterface::class);
        $result->method('getPayload')->willReturn(['data' => 'test']);
        $result->method('getStatusCode')->willReturn(200);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator, $userPerms)
            ->willReturn($result);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['data' => 'test'], 200)
            ->willReturnSelf();

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
