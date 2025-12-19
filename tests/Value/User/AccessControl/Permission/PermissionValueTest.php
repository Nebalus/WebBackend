<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use PHPUnit\Framework\TestCase;

class PermissionValueTest extends TestCase
{
    /**
     * @throws ApiException
     */
    public function testFromValidIntReturnsObject(): void
    {
        $pv = PermissionValue::from(5);
        $this->assertSame(5, $pv->asInt());
    }
}
