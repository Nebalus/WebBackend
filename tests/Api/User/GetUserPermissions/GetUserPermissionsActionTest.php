<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\GetUserPermissions;

use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsAction;
use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsService;
use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class GetUserPermissionsActionTest extends TestCase
{
    private GetUserPermissionsAction $action;
    private GetUserPermissionsValidator&MockObject $validator;
    private GetUserPermissionsService&MockObject $service;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(GetUserPermissionsValidator::class);
        $this->service = $this->createMock(GetUserPermissionsService::class);
        $this->action = new GetUserPermissionsAction($this->service, $this->validator);
    }

    public function testAction(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $result = $this->createMock(ResultInterface::class);
        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);
        $pathArgs = [];

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $request->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                [AttributeTypes::REQUESTING_USER, null, $requestingUser],
                [AttributeTypes::USER_PERMISSION_INDEX, null, $userPerms],
            ]);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator, $requestingUser, $userPerms)
            ->willReturn($result);

        $result->expects($this->once())
            ->method('getPayload')
            ->willReturn(['permissions' => []]);

        $result->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['permissions' => []], 200)
            ->willReturn($response);

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
