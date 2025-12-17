<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Range;

readonly class PermissionAccess
{
    private function __construct(
        private PermissionNode $node,
        private bool $allowAccessWithSubPermission
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(
        string $node,
        bool $allowAccessWithSubPermission = false
    ): self {
        return new self(PermissionNode::from($node), $allowAccessWithSubPermission);
    }

    public function getNode(): PermissionNode
    {
        return $this->node;
    }

    public function isAllowAccessWithSubPermission(): bool
    {
        return $this->allowAccessWithSubPermission;
    }
}
