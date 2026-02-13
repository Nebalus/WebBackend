<?php

namespace UnitTesting\Api\Admin\User\Role\Remove;

use Nebalus\Webapi\Api\Admin\User\Role\Remove\RemoveRoleFromUserAction;
use Nebalus\Webapi\Api\Admin\User\Role\Remove\RemoveRoleFromUserService;
use Nebalus\Webapi\Api\Admin\User\Role\Remove\RemoveRoleFromUserValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class RemoveRoleFromUserActionTest extends TestCase
{
    private RemoveRoleFromUserService $service;
    private RemoveRoleFromUserValidator $validator;
    private RemoveRoleFromUserAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(RemoveRoleFromUserService::class);
        $this->validator = $this->createMock(RemoveRoleFromUserValidator::class);
        $this->action = new RemoveRoleFromUserAction($this->service, $this->validator);
    }

    #[Test]
    public function testInvokeExecutesServiceAndReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = ['user_id' => 1, 'role_id' => 2];
        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);

        $request->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                [AttributeTypes::CLIENT_USER, null, $requestingUser],
                [AttributeTypes::CLIENT_USER_PERMISSION_INDEX, null, $userPerms],
            ]);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $result = $this->createMock(ResultInterface::class);
        $result->method('getPayload')->willReturn(['data' => 'test']);
        $result->method('getStatusCode')->willReturn(200);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator, $requestingUser, $userPerms)
            ->willReturn($result);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['data' => 'test'], 200)
            ->willReturnSelf();

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
