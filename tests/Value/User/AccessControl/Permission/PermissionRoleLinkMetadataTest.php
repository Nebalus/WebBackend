<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkMetadata;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use PHPUnit\Framework\TestCase;

class PermissionRoleLinkMetadataTest extends TestCase
{
    /**
     * @throws ApiException
     */
    public function testFromAndArray(): void
    {
        $meta = PermissionRoleLinkMetadata::from(true, PermissionValue::from(2));
        $this->assertTrue($meta->allowAllSubPermissions());
        $this->assertTrue($meta->hasValue());
        $this->assertSame(2, $meta->getValue()->asInt());

        $arr = $meta->asArray();
        $this->assertArrayHasKey('allow_all_sub_permissions', $arr);
        $this->assertArrayHasKey('value', $arr);

        $meta2 = PermissionRoleLinkMetadata::fromArray(['allow_all_sub_permissions' => false, 'value' => null]);
        $this->assertFalse($meta2->allowAllSubPermissions());
        $this->assertFalse($meta2->hasValue());
    }
}
