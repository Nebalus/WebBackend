<?php

namespace UnitTesting\Api\Admin\Role\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Delete\DeleteRoleResponder;
use Nebalus\Webapi\Api\Admin\Role\Delete\DeleteRoleService;
use Nebalus\Webapi\Api\Admin\Role\Delete\DeleteRoleValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteRoleServiceTest extends TestCase
{
    private MySqlRoleRepository $repository;
    private DeleteRoleResponder $responder;
    private DeleteRoleService $service;
    private DeleteRoleValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(DeleteRoleResponder::class);
        $this->service = new DeleteRoleService($this->repository, $this->responder);
        $this->validator = $this->createMock(DeleteRoleValidator::class);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_ROLE_DELETE && $access->isAllowAccessWithSubPermission() === true;
            }))
            ->willReturn(false);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteReturnsNotFoundWhenRoleDoesNotExist(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);

        $this->repository->expects($this->once())
            ->method('findRoleByRoleId')
            ->with($roleId)
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Role does not exist', $result->getMessage());
    }

    #[Test]
    public function testExecuteReturnsForbiddenWhenRoleIsNotDeletable(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);

        $role = Role::from(
            $roleId,
            RoleName::from('admin'),
            RoleDescription::from('Desc'),
            RoleHexColor::from('FF0000'),
            RoleAccessLevel::from(100),
            true,
            false, // not deletable
            true,
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->repository->expects($this->once())
            ->method('findRoleByRoleId')
            ->with($roleId)
            ->willReturn($role);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
        $this->assertEquals('This role cannot be deleted', $result->getMessage());
    }

    #[Test]
    public function testExecuteDeletesRoleAndReturnsSuccess(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);

        $role = Role::from(
            $roleId,
            RoleName::from('admin'),
            RoleDescription::from('Desc'),
            RoleHexColor::from('FF0000'),
            RoleAccessLevel::from(100),
            true,
            true, // deletable
            true,
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->repository->expects($this->once())
            ->method('findRoleByRoleId')
            ->with($roleId)
            ->willReturn($role);

        $this->repository->expects($this->once())
            ->method('deleteRoleByRoleId')
            ->with($roleId)
            ->willReturn(true);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
