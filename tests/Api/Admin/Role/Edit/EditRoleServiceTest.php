<?php

namespace UnitTesting\Api\Admin\Role\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleResponder;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleService;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleValidator;
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

class EditRoleServiceTest extends TestCase
{
    private MySqlRoleRepository $repository;
    private EditRoleResponder $responder;
    private EditRoleService $service;
    private EditRoleValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(EditRoleResponder::class);
        $this->service = new EditRoleService($this->repository, $this->responder);
        $this->validator = $this->createMock(EditRoleValidator::class);
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
    public function testExecuteReturnsForbiddenWhenRoleIsNotEditable(): void
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
            true,
            false, // not editable
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
        $this->assertEquals('This role cannot be edited', $result->getMessage());
    }

    #[Test]
    public function testExecuteReturnsNotFoundWhenRoleDoesNotExistAfterUpdate(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);
        $this->validator->method('getRoleName')->willReturn(RoleName::from('admin'));
        $this->validator->method('getRoleDescription')->willReturn(RoleDescription::from('Desc'));
        $this->validator->method('getRoleColor')->willReturn(RoleHexColor::from('FF0000'));
        $this->validator->method('getAccessLevel')->willReturn(RoleAccessLevel::from(100));
        $this->validator->method('appliesToEveryone')->willReturn(true);
        $this->validator->method('isDisabled')->willReturn(false);

        $role = Role::from(
            $roleId,
            RoleName::from('admin'),
            RoleDescription::from('Desc'),
            RoleHexColor::from('FF0000'),
            RoleAccessLevel::from(100),
            true,
            true,
            true, // editable
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->repository->expects($this->once())
            ->method('findRoleByRoleId')
            ->with($roleId)
            ->willReturn($role);

        $this->repository->expects($this->once())
            ->method('updateRoleByRoleId')
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Role does not exist', $result->getMessage());
    }

    #[Test]
    public function testExecuteUpdatesRoleAndReturnsSuccess(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);
        $this->validator->method('getRoleName')->willReturn(RoleName::from('admin'));
        $this->validator->method('getRoleDescription')->willReturn(RoleDescription::from('Desc'));
        $this->validator->method('getRoleColor')->willReturn(RoleHexColor::from('FF0000'));
        $this->validator->method('getAccessLevel')->willReturn(RoleAccessLevel::from(100));
        $this->validator->method('appliesToEveryone')->willReturn(true);
        $this->validator->method('isDisabled')->willReturn(false);

        $role = Role::from(
            $roleId,
            RoleName::from('admin'),
            RoleDescription::from('Desc'),
            RoleHexColor::from('FF0000'),
            RoleAccessLevel::from(100),
            true,
            true,
            true, // editable
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->repository->expects($this->once())
            ->method('findRoleByRoleId')
            ->with($roleId)
            ->willReturn($role);

        $this->repository->expects($this->once())
            ->method('updateRoleByRoleId')
            ->willReturn($role);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($role)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
