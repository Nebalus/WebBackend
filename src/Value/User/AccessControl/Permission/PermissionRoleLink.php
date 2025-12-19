<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;

readonly class PermissionRoleLink
{
    private function __construct(
        private PermissionNode $node,
        private PermissionRoleLinkMetadata $metadata
    ) {
    }

    public static function from(PermissionNode $node, bool $affectsAllSubPermissions, ?PermissionValue $value = null): self
    {
        $metadata = PermissionRoleLinkMetadata::from($affectsAllSubPermissions, $value);
        return new self($node, $metadata);
    }

    public static function fromMetadata(PermissionNode $node, PermissionRoleLinkMetadata $metadata): self
    {
        return new self($node, $metadata);
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        return new self(
            PermissionNode::from($data['node']),
            PermissionRoleLinkMetadata::fromArray($data)
        );
    }

    public function asArray(): array
    {
        $cache = [
            'node' => $this->node,
        ];
        return array_merge($cache, $this->metadata->asArray());
    }

    public function getNode(): PermissionNode
    {
        return $this->node;
    }

    public function getMetadata(): PermissionRoleLinkMetadata
    {
        return $this->metadata;
    }
}
