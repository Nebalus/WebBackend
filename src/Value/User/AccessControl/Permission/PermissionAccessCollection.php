<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use IteratorAggregate;
use Traversable;

class PermissionAccessCollection implements IteratorAggregate
{
    private array $permissionAccess;

    private function __construct(PermissionAccess ...$permissionAccess)
    {
        $this->permissionAccess = $permissionAccess;
    }

    public static function fromObjects(PermissionAccess ...$permissionAccess): self
    {
        return new self(...$permissionAccess);
    }

    public function getIterator(): Traversable
    {
        yield from $this->permissionAccess;
    }
}
