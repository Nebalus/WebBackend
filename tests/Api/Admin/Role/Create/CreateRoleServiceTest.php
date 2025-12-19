<?php

namespace UnitTesting\Api\Admin\Role\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Create\CreateRoleResponder;
use Nebalus\Webapi\Api\Admin\Role\Create\CreateRoleService;
use Nebalus\Webapi\Api\Admin\Role\Create\CreateRoleValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateRoleServiceTest extends TestCase
{
    private MySqlRoleRepository $repository;
    private CreateRoleResponder $responder;
    private CreateRoleService $service;
    private CreateRoleValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(CreateRoleResponder::class);
        $this->service = new CreateRoleService($this->repository, $this->responder);
        $this->validator = $this->createMock(CreateRoleValidator::class);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_ROLE_CREATE && $access->isAllowAccessWithSubPermission() === true;
            }))
            ->willReturn(false);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteCreatesRoleAndReturnsSuccess(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);

        $this->validator->method('getRoleName')->willReturn(RoleName::from('admin'));
        $this->validator->method('getRoleDescription')->willReturn(RoleDescription::from('Test Description'));
        $this->validator->method('getRoleColor')->willReturn(RoleHexColor::from('00FF00'));
        $this->validator->method('getAccessLevel')->willReturn(RoleAccessLevel::from(50));
        $this->validator->method('appliesToEveryone')->willReturn(false);
        $this->validator->method('isDisabled')->willReturn(false);

        $this->repository->expects($this->once())
            ->method('insertRole')
            ->with($this->isInstanceOf(Role::class))
            ->willReturnArgument(0);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($this->isInstanceOf(Role::class))
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
