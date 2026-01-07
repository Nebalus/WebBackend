<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use IteratorAggregate;
use Traversable;

class PermissionNodeCollection implements IteratorAggregate
{
    private array $permissionNodes;

    private function __construct(PermissionNode ...$permissionNodes)
    {
        $this->permissionNodes = $permissionNodes;
    }

    public static function fromObjects(PermissionNode ...$permissionNodes): self
    {
        return new self(...$permissionNodes);
    }

    public function getIterator(): Traversable
    {
        yield from $this->permissionNodes;
    }
}
