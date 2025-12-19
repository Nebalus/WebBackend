<?php

namespace UnitTesting\Api\Admin\Permission\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionResponder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\Permission;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionId;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionDescription;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionPrestigeLevel;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetPermissionResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $permissionId = PermissionId::from(123);
        $node = PermissionNode::from('admin.test');
        $description = PermissionDescription::from('Test Description');
        $defaultValue = PermissionValue::from(1);
        $prestigeLevel = PermissionPrestigeLevel::from('HIGH');

        $permission = $this->createMock(Permission::class);
        $permission->method('getPermissionId')->willReturn($permissionId);
        $permission->method('getNode')->willReturn($node);
        $permission->method('getDescription')->willReturn($description);
        $permission->method('getDefaultValue')->willReturn($defaultValue);
        $permission->method('getPrestigeLevel')->willReturn($prestigeLevel);

        $responder = new GetPermissionResponder();
        $result = $responder->render($permission);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Permission fetched', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];
        $this->assertEquals(123, $data['permission_id']);
        $this->assertEquals('admin.test', $data['node']);
        $this->assertEquals('Test Description', $data['description']);
        $this->assertEquals(1, $data['default_value']);
        $this->assertEquals('HIGH', $data['prestige_level']['type']);
        $this->assertEquals(2, $data['prestige_level']['value']);
    }
}
