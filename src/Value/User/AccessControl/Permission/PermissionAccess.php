<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Range;

readonly class PermissionAccess
{
    private function __construct(
        private PermissionNode $node,
        private bool $allowAccessWithSubPermission,
        //  private ?Range $valueRange
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(
        string $node,
        bool $allowAccessWithSubPermission = false
    ): self {
        return new self(
            PermissionNode::from($node),
            $allowAccessWithSubPermission,
            //   $valueRange
        );
    }

    public function getNode(): PermissionNode
    {
        return $this->node;
    }

    public function isAllowAccessWithSubPermission(): bool
    {
        return $this->allowAccessWithSubPermission;
    }

//    public function getValueRange(): ?Range
//    {
//        return $this->valueRange;
//    }

//    public function hasValueRange(): bool
//    {
//        return $this->valueRange !== null;
//    }
}
