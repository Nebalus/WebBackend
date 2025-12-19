<?php

namespace UnitTesting\Api\Admin\Role\Permission\Delete;

use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionAction;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionService;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class DeleteRolePermissionActionTest extends TestCase
{
    private DeleteRolePermissionService $service;
    private DeleteRolePermissionValidator $validator;
    private DeleteRolePermissionAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(DeleteRolePermissionService::class);
        $this->validator = $this->createMock(DeleteRolePermissionValidator::class);
        $this->action = new DeleteRolePermissionAction($this->service, $this->validator);
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
