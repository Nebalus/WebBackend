<?php

namespace UnitTesting\Api\Admin\Permission\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Permission\GetAll\GetAllPermissionResponder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\Permission;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionDescription;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionId;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionPrestigeLevel;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllPermissionResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $permission1 = Permission::from(
            PermissionId::from(1),
            PermissionNode::from('node.one'),
            PermissionDescription::from('Description one'),
            PermissionPrestigeLevel::from('HIGH'),
            PermissionValue::from(1)
        );

        $permission2 = Permission::from(
            PermissionId::from(2),
            PermissionNode::from('node.two'),
            PermissionDescription::from('Description two'),
            PermissionPrestigeLevel::from('LOW'),
            PermissionValue::from(0)
        );

        $collection = PermissionCollection::fromObjects($permission1, $permission2);

        $responder = new GetAllPermissionResponder();
        $result = $responder->render($collection);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('List of permissions found', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['permission_id']);
        $this->assertEquals('node.one', $data[0]['node']);
        $this->assertEquals('Description one', $data[0]['description']);
        $this->assertEquals(1, $data[0]['default_value']);
        $this->assertEquals('HIGH', $data[0]['prestige_level']['type']);
        $this->assertEquals(2, $data[0]['prestige_level']['value']);

        $this->assertEquals(2, $data[1]['permission_id']);
        $this->assertEquals('node.two', $data[1]['node']);
        $this->assertEquals('Description two', $data[1]['description']);
        $this->assertEquals(0, $data[1]['default_value']);
        $this->assertEquals('LOW', $data[1]['prestige_level']['type']);
        $this->assertEquals(4, $data[1]['prestige_level']['value']);
    }
}
