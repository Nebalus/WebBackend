<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionPrestigeLevel;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PermissionPrestigeLevelTest extends TestCase
{
    /**
     * @throws ApiException
     */
    public function testFromValidLevels(): void
    {
        $p = PermissionPrestigeLevel::from('critical');
        $this->assertSame(1, $p->asInt());
        $this->assertSame('CRITICAL', $p->asString());

        $p = PermissionPrestigeLevel::from('LOW');
        $this->assertSame(4, $p->asInt());
        $this->assertSame('LOW', $p->asString());
    }

    /**
     * @throws ApiException
     */
    public function testFromInvalidLevelThrows(): void
    {
        $this->expectException(ApiInvalidArgumentException::class);
        PermissionPrestigeLevel::from('unknown');
    }
}
