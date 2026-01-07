<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use IteratorAggregate;
use Traversable;

class PermissionRoleLinkCollection implements IteratorAggregate
{
    private array $permissionNodes = [];

    private function __construct(PermissionRoleLink ...$permissionNodes)
    {
        $this->permissionNodes = $permissionNodes;
    }

    public static function fromObjects(PermissionRoleLink ...$permissionNodes): self
    {
        return new self(...$permissionNodes);
    }

    public function isEmpty(): bool
    {
        return count($this->permissionNodes) === 0;
    }

    public function getIterator(): Traversable
    {
        yield from $this->permissionNodes;
    }
}
