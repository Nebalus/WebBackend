<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionDescription;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PermissionDescriptionTest extends TestCase
{
    /**
     * @throws ApiInvalidArgumentException
     */
    public function testFromValidDescription(): void
    {
        $description = PermissionDescription::from('Can read user data (limited)');
        $this->assertSame('Can read user data (limited)', $description->asString());
    }

    public function testFromInvalidDescriptionThrows(): void
    {
        $this->expectException(ApiInvalidArgumentException::class);
        // Use random characters to break regex
        PermissionDescription::from("invalid description \x01\x02");
    }
}
