<?php

namespace UnitTesting\Api\Admin\Permission\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Permission\GetAll\GetAllPermissionResponder;
use Nebalus\Webapi\Api\Admin\Permission\GetAll\GetAllPermissionService;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\PermissionsRepository\MySqlPermissionRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllPermissionServiceTest extends TestCase
{
    private MySqlPermissionRepository $repository;
    private GetAllPermissionResponder $responder;
    private GetAllPermissionService $service;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlPermissionRepository::class);
        $this->responder = $this->createMock(GetAllPermissionResponder::class);
        $this->service = new GetAllPermissionService($this->responder, $this->repository);
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
    public function testExecuteReturnsSuccessWithPermissions(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);

        $collection = $this->createMock(PermissionCollection::class);

        $this->repository->expects($this->once())
            ->method('getAllPermissions')
            ->willReturn($collection);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($collection)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
