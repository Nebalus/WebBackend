<?php

namespace UnitTesting\Api\Admin\Role\Permission\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionResponder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteRolePermissionResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $link1 = PermissionRoleLink::from(PermissionNode::from('test.node.one'), true);
        $link2 = PermissionRoleLink::from(PermissionNode::from('test.node.two'), false);

        $collection = PermissionRoleLinkCollection::fromObjects($link1, $link2);

        $responder = new DeleteRolePermissionResponder();
        $result = $responder->render($collection);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('All requested permissions deleted successfully', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(2, $data['deleted_permissions']);
        $this->assertEquals('test.node.one', $data['deleted_permissions'][0]['node']);
        $this->assertTrue($data['deleted_permissions'][0]['allow_all_sub_permissions']);

        $this->assertEquals('test.node.two', $data['deleted_permissions'][1]['node']);
        $this->assertFalse($data['deleted_permissions'][1]['allow_all_sub_permissions']);
    }
}
