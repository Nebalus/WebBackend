<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Role;

use IteratorAggregate;
use Traversable;

class RoleCollection implements IteratorAggregate
{
    private array $roles;

    private function __construct(Role ...$roles)
    {
        $this->roles = $roles;
    }

    public static function fromObjects(Role ...$roles): self
    {
        return new self(...$roles);
    }

    public function toArray(): array
    {
        return $this->roles;
    }

    public function getIterator(): Traversable
    {
        yield from $this->roles;
    }
}
