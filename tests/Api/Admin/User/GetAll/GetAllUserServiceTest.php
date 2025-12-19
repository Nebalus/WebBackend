<?php

namespace UnitTesting\Api\Admin\User\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\User\GetAll\GetAllUserResponder;
use Nebalus\Webapi\Api\Admin\User\GetAll\GetAllUserService;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllUserServiceTest extends TestCase
{
    private MySqlUserRepository $repository;
    private GetAllUserResponder $responder;
    private GetAllUserService $service;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MySqlUserRepository::class);
        $this->responder = $this->createMock(GetAllUserResponder::class);
        $this->service = new GetAllUserService($this->responder, $this->repository);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_USER && $access->isAllowAccessWithSubPermission() === true;
            }))
            ->willReturn(false);

        $result = $this->service->execute($this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteReturnsSuccessWithUsers(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);

        $users = [$this->createMock(UserAccount::class)];

        $this->repository->expects($this->once())
            ->method('getAllUsers')
            ->willReturn($users);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($users)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
