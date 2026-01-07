<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use IteratorAggregate;
use Traversable;

class PermissionCollection implements IteratorAggregate
{
    private array $permissions;

    private function __construct(Permission ...$permissions)
    {
        $this->permissions = $permissions;
    }

    public static function fromObjects(Permission ...$permissions): self
    {
        return new self(...$permissions);
    }

    public function getNodeCollection(): PermissionNodeCollection
    {
        return PermissionNodeCollection::fromObjects(...array_map(
            fn(Permission $permission) => $permission->getNode(),
            $this->permissions
        ));
    }

    public function getIterator(): Traversable
    {
        yield from $this->permissions;
    }
}
