<?php

namespace UnitTesting\Api\Admin\Permission\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionResponder;
use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionService;
use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\PermissionsRepository\MySqlPermissionRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\Permission;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionId;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use PHPUnit\Framework\TestCase;

class GetPermissionServiceTest extends TestCase
{
    private MySqlPermissionRepository $repository;
    private GetPermissionResponder $responder;
    private GetPermissionService $service;
    private GetPermissionValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlPermissionRepository::class);
        $this->responder = $this->createMock(GetPermissionResponder::class);
        $this->service = new GetPermissionService($this->repository, $this->responder);
        $this->validator = $this->createMock(GetPermissionValidator::class);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_ROLE && $access->isAllowAccessWithSubPermission() === true;
            }))
            ->willReturn(false);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    public function testExecuteReturnsNotFoundWhenPermissionNotFound(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);

        $permissionId = $this->createMock(PermissionId::class);
        $this->validator->method('getPermissionId')->willReturn($permissionId);

        $this->repository->expects($this->once())
            ->method('findPermissionByPermissionId')
            ->with($permissionId)
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Permission not found', $result->getMessage());
    }

    public function testExecuteReturnsSuccessWhenPermissionFound(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);

        $permissionId = $this->createMock(PermissionId::class);
        $this->validator->method('getPermissionId')->willReturn($permissionId);

        $permission = $this->createMock(Permission::class);
        $this->repository->expects($this->once())
            ->method('findPermissionByPermissionId')
            ->with($permissionId)
            ->willReturn($permission);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($permission)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
