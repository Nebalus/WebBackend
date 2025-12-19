<?php

namespace UnitTesting\Api\Admin\Role\Permission\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionResponder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllRolePermissionResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $link1 = PermissionRoleLink::from(PermissionNode::from('test.node.one'), true);
        $link2 = PermissionRoleLink::from(PermissionNode::from('test.node.two'), false);

        $collection = PermissionRoleLinkCollection::fromObjects($link1, $link2);

        $responder = new GetAllRolePermissionResponder();
        $result = $responder->render($collection);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Fetched all permissions from this role', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(2, $data);
        $this->assertEquals('test.node.one', $data[0]['node']);
        $this->assertTrue($data[0]['allow_all_sub_permissions']);

        $this->assertEquals('test.node.two', $data[1]['node']);
        $this->assertFalse($data[1]['allow_all_sub_permissions']);
    }
}
