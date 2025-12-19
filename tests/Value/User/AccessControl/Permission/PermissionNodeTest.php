<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PermissionNodeTest extends TestCase
{
    /**
     * @throws ApiException
     */
    public function testFromValidNode(): void
    {
        $node = PermissionNode::from('user.read');
        $this->assertSame('user.read', $node->asString());
    }

    /**
     * @throws ApiException
     */
    public function testFromInvalidNodeThrows(): void
    {
        $this->expectException(ApiInvalidArgumentException::class);
        PermissionNode::from('Invalid Node With Spaces');
    }
}
