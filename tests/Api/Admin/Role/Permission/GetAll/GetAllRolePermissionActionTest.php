<?php

namespace UnitTesting\Api\Admin\Role\Permission\GetAll;

use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionAction;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionService;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class GetAllRolePermissionActionTest extends TestCase
{
    private GetAllRolePermissionService $service;
    private GetAllRolePermissionValidator $validator;
    private GetAllRolePermissionAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(GetAllRolePermissionService::class);
        $this->validator = $this->createMock(GetAllRolePermissionValidator::class);
        $this->action = new GetAllRolePermissionAction($this->service, $this->validator);
    }

    #[Test]
    public function testInvokeExecutesServiceAndReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = ['role_id' => 1];
        $userPerms = $this->createMock(UserPermissionIndex::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(AttributeTypes::USER_PERMISSION_INDEX)
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
