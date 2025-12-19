<?php

namespace UnitTesting\Api\Admin\User\Role\Add;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\User\Role\Add\AddRoleToUserResponder;
use Nebalus\Webapi\Api\Admin\User\Role\Add\AddRoleToUserService;
use Nebalus\Webapi\Api\Admin\User\Role\Add\AddRoleToUserValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AddRoleToUserServiceTest extends TestCase
{
    private MySqlUserRepository $userRepository;
    private MySqlRoleRepository $roleRepository;
    private AddRoleToUserResponder $responder;
    private AddRoleToUserService $service;
    private AddRoleToUserValidator $validator;
    private UserAccount $requestingUser;
    private UserPermissionIndex $userPerms;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
        $this->roleRepository = $this->createMock(MySqlRoleRepository::class);
        $this->responder = $this->createMock(AddRoleToUserResponder::class);
        $this->service = new AddRoleToUserService($this->userRepository, $this->roleRepository, $this->responder);
        $this->validator = $this->createMock(AddRoleToUserValidator::class);
        $this->requestingUser = $this->createMock(UserAccount::class);
        $this->userPerms = $this->createMock(UserPermissionIndex::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionResultWhenUserHasNoAccess(): void
    {
        $this->userPerms->expects($this->once())
            ->method('hasAccessTo')
            ->with($this->callback(function (PermissionAccess $access) {
                return $access->getNode()->asString() === PermissionNodeTypes::ADMIN_USER_ROLE_ADD && $access->isAllowAccessWithSubPermission() === true;
            }))
            ->willReturn(false);

        $result = $this->service->execute($this->validator, $this->requestingUser, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteReturnsNotFoundWhenRoleDoesNotExist(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);

        $this->roleRepository->expects($this->once())
            ->method('findRoleByRoleId')
            ->with($roleId)
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $this->requestingUser, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals("The role you want add to does not exist under this id", $result->getMessage());
    }

    #[Test]
    public function testExecuteReturnsForbiddenWhenRoleIsAboveRequestingUserRole(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);
        $userId = UserId::from(1);
        $this->requestingUser->method('getUserId')->willReturn($userId);

        $roleToAdd = $this->createRole(100); // Access level 100
        $this->roleRepository->method('findRoleByRoleId')->willReturn($roleToAdd);

        $userRole = $this->createRole(100); // Access level 100 (same or higher means forbidden logic in service seems to be: if userRole >= roleToAdd then forbidden? Wait.)
        // Code: if ($highestRoleOfRequestingUser->getAccessLevel()->asInt() >= $roleThatWantsToBeAdded->getAccessLevel()->asInt())
        // Wait, usually higher number means higher access.
        // If my role is 100 and I want to add role 100, I should be able to?
        // Or maybe lower number is higher access?
        // Let's assume standard: higher number = higher access.
        // If I am 100, I can add roles < 100. I cannot add roles >= 100.
        // The code says: if (user >= target) return forbidden.
        // This means I cannot add a role that is equal or higher than mine.
        // Which makes sense if I can only grant permissions lower than mine.

        $this->userRepository->method('getAllRolesFromUserByUserId')
            ->willReturn(RoleCollection::fromObjects($userRole));

        $result = $this->service->execute($this->validator, $this->requestingUser, $this->userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
        $this->assertEquals("You cannot add this role to this user because this role is above your highest role", $result->getMessage());
    }

    #[Test]
    public function testExecuteAddsRoleAndReturnsSuccess(): void
    {
        $this->userPerms->method('hasAccessTo')->willReturn(true);
        $roleId = RoleId::from(1);
        $this->validator->method('getRoleId')->willReturn($roleId);
        $userId = UserId::from(1);
        $this->requestingUser->method('getUserId')->willReturn($userId);
        $targetUserId = UserId::from(2);
        $this->validator->method('getUserId')->willReturn($targetUserId);

        $roleToAdd = $this->createRole(150); // Access level 150 (weaker than 100)
        $this->roleRepository->method('findRoleByRoleId')->willReturn($roleToAdd);

        $userRole = $this->createRole(100); // Access level 100
        $this->userRepository->method('getAllRolesFromUserByUserId')
            ->willReturn(RoleCollection::fromObjects($userRole));

        $this->userRepository->expects($this->once())
            ->method('insertRoleToUserByRoleId')
            ->with($targetUserId, $roleId)
            ->willReturn(true);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($roleToAdd)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $this->requestingUser, $this->userPerms);

        $this->assertSame($expectedResult, $result);
    }

    private function createRole(int $accessLevel): Role
    {
        return Role::from(
            RoleId::from(rand(1, 1000)),
            RoleName::from('role'),
            RoleDescription::from('desc'),
            RoleHexColor::from('000000'),
            RoleAccessLevel::from($accessLevel),
            true,
            true,
            true,
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }
}
