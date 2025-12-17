<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Exception\ApiException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class PermissionAccessTest extends TestCase
{
    /**
     * @throws ApiException
     */
    #[TestWith(['user.read', true])]
    #[TestWith(['user.settings', false])]
    public function testFromAndFlags(string $node, bool $allowAccessWithSubPermission): void
    {
        $access = PermissionAccess::from($node, $allowAccessWithSubPermission);
        $this->assertSame($node, $access->getNode()->asString());
        $this->assertSame($allowAccessWithSubPermission, $access->isAllowAccessWithSubPermission());
    }
}
