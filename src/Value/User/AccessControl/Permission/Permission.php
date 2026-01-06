<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

readonly class Permission
{
    private function __construct(
        private PermissionId $permissionId,
        private PermissionNode $node,
        private ?PermissionDescription $description,
        private PermissionPrestigeLevel $prestigeLevel,
        private ?PermissionValue $defaultValue
    ) {
    }

    public static function from(
        PermissionId $permissionId,
        PermissionNode $node,
        ?PermissionDescription $description,
        PermissionPrestigeLevel $prestigeLevel,
        ?PermissionValue $defaultValue = null
    ): self {
        return new self(
            $permissionId,
            $node,
            $description,
            $prestigeLevel,
            $defaultValue
        );
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public static function fromArray(array $value): self
    {
        $permissionId = PermissionId::from($value['permission_id']);
        $node = PermissionNode::from($value['node']);
        $description = empty($value['description']) ? null : PermissionDescription::from($value['description']);
        $prestigeLevel = PermissionPrestigeLevel::from($value['prestige_level']);
        $defaultValue = empty($value['default_value']) ? null : PermissionValue::from($value['default_value']);

        return new self($permissionId, $node, $description, $prestigeLevel, $defaultValue);
    }

    public function asArray(): array
    {
        return [
            'permission_id' => $this->permissionId->asInt(),
            'node' => $this->node->asString(),
            'description' => $this->description?->asString(),
            'prestige_level' => $this->prestigeLevel->asString(),
            'default_value' => $this->defaultValue?->asInt(),
        ];
    }

    public function getPermissionId(): PermissionId
    {
        return $this->permissionId;
    }

    public function getNode(): PermissionNode
    {
        return $this->node;
    }

    public function getDescription(): ?PermissionDescription
    {
        return $this->description;
    }

    public function getPrestigeLevel(): PermissionPrestigeLevel
    {
        return $this->prestigeLevel;
    }

    public function getDefaultValue(): ?PermissionValue
    {
        return $this->defaultValue;
    }

    public function hasDefaultValue(): bool
    {
        return $this->defaultValue !== null;
    }
}
