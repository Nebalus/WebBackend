<?php

namespace UnitTesting\Api\Admin\User\Role\Add;

use Nebalus\Webapi\Api\Admin\User\Role\Add\AddRoleToUserAction;
use Nebalus\Webapi\Api\Admin\User\Role\Add\AddRoleToUserService;
use Nebalus\Webapi\Api\Admin\User\Role\Add\AddRoleToUserValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class AddRoleToUserActionTest extends TestCase
{
    private AddRoleToUserService $service;
    private AddRoleToUserValidator $validator;
    private AddRoleToUserAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(AddRoleToUserService::class);
        $this->validator = $this->createMock(AddRoleToUserValidator::class);
        $this->action = new AddRoleToUserAction($this->validator, $this->service);
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
                [AttributeTypes::REQUESTING_USER, null, $requestingUser],
                [AttributeTypes::USER_PERMISSION_INDEX, null, $userPerms],
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
