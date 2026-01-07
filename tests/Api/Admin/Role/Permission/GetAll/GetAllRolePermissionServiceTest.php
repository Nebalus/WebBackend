<?php

namespace UnitTesting\Api\Admin\Role\Permission\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionResponder;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionService;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllRolePermissionServiceTest extends TestCase
{
    private MySqlRoleRepository $repository;
    private GetAllRolePermissionResponder $responder;
    private GetAllRolePermissionService $service;
    private GetAllRolePermissionValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(GetAllRolePermissionResponder::class);
        $this->service = new GetAllRolePermissionService($this->repository, $this->responder);
        $this->validator = $this->createMock(GetAllRolePermissionValidator::class);
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
    public function testExecuteReturnsSuccessWithPermissions(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);

        $collection = $this->createMock(PermissionRoleLinkCollection::class);

        $this->repository->expects($this->once())
            ->method('getAllPermissionLinksByRoleId')
            ->with($roleId)
            ->willReturn($collection);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($collection)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
