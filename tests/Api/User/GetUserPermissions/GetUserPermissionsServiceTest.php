<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\GetUserPermissions;

use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsResponder;
use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsService;
use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsValidator;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetUserPermissionsServiceTest extends TestCase
{
    private GetUserPermissionsService $service;
    private GetUserPermissionsResponder&MockObject $responder;
    private MySqlRoleRepository&MockObject $roleRepository;

    protected function setUp(): void
    {
        $this->responder = $this->createMock(GetUserPermissionsResponder::class);
        $this->roleRepository = $this->createMock(MySqlRoleRepository::class);
        $this->service = new GetUserPermissionsService($this->responder, $this->roleRepository);
    }

    public function testExecuteSameUser(): void
    {
        $validator = $this->createMock(GetUserPermissionsValidator::class);
        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userId = $this->createMock(UserId::class);

        $validator->expects($this->once())
            ->method('getUserId')
            ->willReturn($userId);

        $requestingUser->expects($this->any())
            ->method('getUserId')
            ->willReturn($userId);

        $this->responder->expects($this->once())
            ->method('render')
            ->with($userId, $userPerms)
            ->willReturn($this->createMock(ResultInterface::class));

        $this->service->execute($validator, $requestingUser, $userPerms);
    }

    public function testExecuteDifferentUser(): void
    {
        $validator = $this->createMock(GetUserPermissionsValidator::class);
        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userId = $this->createMock(UserId::class);
        $otherUserId = $this->createMock(UserId::class);
        $otherUserPerms = $this->createMock(UserPermissionIndex::class);

        $validator->expects($this->once())
            ->method('getUserId')
            ->willReturn($otherUserId);

        $requestingUser->expects($this->any())
            ->method('getUserId')
            ->willReturn($userId);

        $this->roleRepository->expects($this->once())
            ->method('getPermissionIndexFromUserId')
            ->with($otherUserId)
            ->willReturn($otherUserPerms);

        $this->responder->expects($this->once())
            ->method('render')
            ->with($otherUserId, $otherUserPerms)
            ->willReturn($this->createMock(ResultInterface::class));

        $this->service->execute($validator, $requestingUser, $userPerms);
    }
}
