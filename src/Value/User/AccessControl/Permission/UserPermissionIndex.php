<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use IteratorAggregate;
use Nebalus\Webapi\Exception\ApiException;
use Traversable;

class UserPermissionIndex implements IteratorAggregate
{
    private array $permissionNodeIndexList = [];

    private function __construct(array $permissionNodeIndex)
    {
        $this->permissionNodeIndexList = $permissionNodeIndex;
    }

    /**
     * @throws ApiException
     */
    public static function fromPermissionRoleLinkCollections(PermissionRoleLinkCollection ...$privilegeRoleLinkCollections): self
    {
        $cache = [];
        foreach ($privilegeRoleLinkCollections as $privilegeRoleLinkCollection) {
            foreach ($privilegeRoleLinkCollection as $privilegeRoleLink) {
//                var_dump($privilegeRoleLink->getMetadata()->getValue()->asInt());
//                if (key_exists($privilegeRoleLink->getNode()->asString(), $cache)) {
//                    $currentMetadata = $cache[$privilegeRoleLink->getNode()->asString()];
//                    if ($currentMetadata->hasValue() && !$privilegeRoleLink->getMetadata()->hasValue()) {
//                        continue;
//                    }
//                }
                $cache[$privilegeRoleLink->getNode()->asString()] = $privilegeRoleLink->getMetadata();
            }
        }
        return new self($cache);
    }

    public function hasAccessTo(PermissionAccess $permissionAccess): bool
    {
        $node = $permissionAccess->getNode()->asString();
        $parts = explode('.', $node);
        $currentNode = '';

        foreach ($parts as $index => $part) {
            $currentNode = $index === 0 ? $part : "$currentNode.$part";
            if (!array_key_exists($currentNode, $this->permissionNodeIndexList)) {
                continue;
            }

            $permissionMetadata = $this->permissionNodeIndexList[$currentNode];
            if (!$permissionMetadata instanceof PermissionRoleLinkMetadata) {
                continue;
            }

            if ($permissionAccess->isAllowAccessWithSubPermission() && str_starts_with($currentNode, $permissionAccess->getNode()->asString())) {
                return true;
            }

            if ($permissionMetadata->allowAllSubPermissions()) {
                return true;
            }
        }

        if ($permissionAccess->isAllowAccessWithSubPermission()) {
            $keys = array_keys($this->permissionNodeIndexList);
            $pattern = '/' . preg_quote($permissionAccess->getNode()->asString(), '/') . '/';
            return count(preg_grep($pattern, $keys)) > 0;
        }

        return false;
    }

    public function hasAccessToAtLeastOneNode(PermissionAccessCollection $permissionAccessCollection): bool
    {
        foreach ($permissionAccessCollection as $permissionAccess) {
            if ($this->hasAccessTo($permissionAccess)) {
                return true;
            }
        }
        return false;
    }

    public function asArray(): array
    {
        return $this->permissionNodeIndexList;
    }

    public function getIterator(): Traversable
    {
        yield from $this->permissionNodeIndexList;
    }
}
