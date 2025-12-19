<?php

namespace UnitTesting\Api\Admin\Role\Create;

use Nebalus\Webapi\Api\Admin\Role\Create\CreateRoleAction;
use Nebalus\Webapi\Api\Admin\Role\Create\CreateRoleService;
use Nebalus\Webapi\Api\Admin\Role\Create\CreateRoleValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class CreateRoleActionTest extends TestCase
{
    private CreateRoleService $service;
    private CreateRoleValidator $validator;
    private CreateRoleAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(CreateRoleService::class);
        $this->validator = $this->createMock(CreateRoleValidator::class);
        $this->action = new CreateRoleAction($this->validator, $this->service);
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
            ->with(AttributeTypes::USER_PERMISSION_INDEX)
            ->willReturn($userPerms);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $result = $this->createMock(ResultInterface::class);
        $result->method('getPayload')->willReturn(['data' => 'test']);
        $result->method('getStatusCode')->willReturn(201);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator, $userPerms)
            ->willReturn($result);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['data' => 'test'], 201)
            ->willReturnSelf();

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
