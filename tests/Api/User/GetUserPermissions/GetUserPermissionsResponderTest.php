<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\GetUserPermissions;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsResponder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\TestCase;

class GetUserPermissionsResponderTest extends TestCase
{
    private GetUserPermissionsResponder $responder;

    protected function setUp(): void
    {
        $this->responder = new GetUserPermissionsResponder();
    }

    public function testRender(): void
    {
        $userId = $this->createMock(UserId::class);
        $userPermissionIndex = $this->createMock(UserPermissionIndex::class);

        $userId->expects($this->once())
            ->method('asInt')
            ->willReturn(1);

        $permissionMock = $this->createMock(\stdClass::class); // Mocking permission object
        // Since asArray returns array of objects/arrays, we need to mock that structure.
        // But UserPermissionIndex::asArray() returns an array of Permission objects?
        // Let's check UserPermissionIndex.
        // Assuming asArray returns array of objects that have asArray method.
        // Wait, the responder code:
        // $permissions = array_map(function ($permission) { return $permission->asArray(); }, $userPermissionIndex->asArray());
        // So UserPermissionIndex::asArray() returns an array of objects that have asArray().

        // I'll mock the permission object.
        $permission = new class {
            public function asArray(): array
            {
                return ['id' => 1, 'name' => 'perm'];
            }
        };

        $userPermissionIndex->expects($this->once())
            ->method('asArray')
            ->willReturn([$permission]);

        $result = $this->responder->render($userId, $userPermissionIndex);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'List of all permissions for the requested user',
            'status_code' => 200,
            'payload' => [
                'user_id' => 1,
                'permissions' => [
                    ['id' => 1, 'name' => 'perm']
                ]
            ]
        ], $result->getPayload());
    }
}
