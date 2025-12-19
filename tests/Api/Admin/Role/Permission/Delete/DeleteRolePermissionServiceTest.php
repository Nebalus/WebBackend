<?php

namespace UnitTesting\Api\Admin\Role\Permission\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionResponder;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionService;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteRolePermissionServiceTest extends TestCase
{
    private MySqlRoleRepository $repository;
    private DeleteRolePermissionResponder $responder;
    private DeleteRolePermissionService $service;
    private DeleteRolePermissionValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(DeleteRolePermissionResponder::class);
        $this->service = new DeleteRolePermissionService($this->repository, $this->responder);
        $this->validator = $this->createMock(DeleteRolePermissionValidator::class);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_ROLE_EDIT && $access->isAllowAccessWithSubPermission() === true;
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
    public function testExecuteDeletesPermissionsAndReturnsSuccess(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);

        $node1 = PermissionNode::from('test.node.one');
        $node2 = PermissionNode::from('test.node.two');
        $permissionNodes = PermissionNodeCollection::fromObjects($node1, $node2);
        $this->validator->method('getPermissionNodes')->willReturn($permissionNodes);

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

        $link1 = PermissionRoleLink::from($node1, true);
        $link2 = PermissionRoleLink::from($node2, false);

        // Before: has link1 and link2
        $this->repository->expects($this->exactly(2))
            ->method('getAllPermissionLinksByRoleId')
            ->with($roleId)
            ->willReturnOnConsecutiveCalls(
                PermissionRoleLinkCollection::fromObjects($link1, $link2), // Before
                PermissionRoleLinkCollection::fromObjects() // After (empty)
            );

        $this->repository->expects($this->once())
            ->method('deletePermissionLinksByRoleId')
            ->with($roleId, $permissionNodes);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($this->callback(function (PermissionRoleLinkCollection $diff) {
                // Diff should contain link1 and link2 because they were deleted (present before, absent after)
                return !$diff->isEmpty();
            }))
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
