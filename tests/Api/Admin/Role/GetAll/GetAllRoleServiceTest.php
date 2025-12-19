<?php

namespace UnitTesting\Api\Admin\Role\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\GetAll\GetAllRoleResponder;
use Nebalus\Webapi\Api\Admin\Role\GetAll\GetAllRoleService;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllRoleServiceTest extends TestCase
{
    private MySqlRoleRepository $repository;
    private GetAllRoleResponder $responder;
    private GetAllRoleService $service;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(GetAllRoleResponder::class);
        $this->service = new GetAllRoleService($this->responder, $this->repository);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_ROLE && $access->isAllowAccessWithSubPermission() === true;
            }))
            ->willReturn(false);

        $result = $this->service->execute($this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteReturnsSuccessWithRoles(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);

        $roleCollection = $this->createMock(RoleCollection::class);

        $this->repository->expects($this->once())
            ->method('getAllRoles')
            ->willReturn($roleCollection);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($roleCollection)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
