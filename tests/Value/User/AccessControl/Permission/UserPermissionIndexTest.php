<?php

namespace UnitTesting\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccessCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use Nebalus\Webapi\Exception\ApiException;
use PHPUnit\Framework\TestCase;

class UserPermissionIndexTest extends TestCase
{
    /**
     * @throws ApiException
     */
    public function testHasAccessToDirectAndSubPermissions(): void
    {
        $link1 = PermissionRoleLink::from(PermissionNode::from('user'), true, null);
        $link2 = PermissionRoleLink::from(PermissionNode::from('user.read'), false, PermissionValue::from(1));

        $collection = PermissionRoleLinkCollection::fromObjects($link1, $link2);
        $index = UserPermissionIndex::fromPermissionRoleLinkCollections($collection);

        $access1 = PermissionAccess::from('user.read', false);
        $this->assertTrue($index->hasAccessTo($access1));

        $access2 = PermissionAccess::from('user.delete', false);
        $this->assertTrue($index->hasAccessTo($access2));

        $access3 = PermissionAccess::from('admin', false);
        $this->assertFalse($index->hasAccessTo($access3));
    }

    /**
     * @throws ApiException
     */
    public function testHasAccessToAtLeastOneNode(): void
    {
        $link = PermissionRoleLink::from(PermissionNode::from('user.read'), false, PermissionValue::from(1));
        $collection = PermissionRoleLinkCollection::fromObjects($link);
        $index = UserPermissionIndex::fromPermissionRoleLinkCollections($collection);

        $accessCollection = PermissionAccessCollection::fromObjects(
            PermissionAccess::from('user.read', false),
            PermissionAccess::from('user.update', false)
        );

        $this->assertTrue($index->hasAccessToAtLeastOneNode($accessCollection));

        $accessCollection2 = PermissionAccessCollection::fromObjects(
            PermissionAccess::from('admin', false)
        );
        $this->assertFalse($index->hasAccessToAtLeastOneNode($accessCollection2));
    }
}
