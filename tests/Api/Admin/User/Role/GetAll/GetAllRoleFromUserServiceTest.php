<?php

namespace UnitTesting\Api\Admin\User\Role\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\User\Role\GetAll\GetAllRoleFromUserResponder;
use Nebalus\Webapi\Api\Admin\User\Role\GetAll\GetAllRoleFromUserService;
use Nebalus\Webapi\Api\Admin\User\Role\GetAll\GetAllRoleFromUserValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllRoleFromUserServiceTest extends TestCase
{
    private MySqlUserRepository $repository;
    private GetAllRoleFromUserResponder $responder;
    private GetAllRoleFromUserService $service;
    private GetAllRoleFromUserValidator $validator;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlUserRepository::class);
        $this->responder = $this->createMock(GetAllRoleFromUserResponder::class);
        $this->service = new GetAllRoleFromUserService($this->repository, $this->responder);
        $this->validator = $this->createMock(GetAllRoleFromUserValidator::class);
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

        $result = $this->service->execute($this->validator, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteReturnsSuccessWithRoles(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $userId = UserId::from(1);
        $this->validator->method('getUserId')->willReturn($userId);

        $collection = $this->createMock(RoleCollection::class);

        $this->repository->expects($this->once())
            ->method('getAllRolesFromUserByUserId')
            ->with($userId)
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
