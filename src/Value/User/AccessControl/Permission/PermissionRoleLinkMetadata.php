<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use JsonSerializable;
use Nebalus\Webapi\Exception\ApiException;

readonly class PermissionRoleLinkMetadata implements JsonSerializable
{
    private function __construct(
        private bool $allowAllSubPermissions,
        private ?PermissionValue $value
    ) {
    }

    public static function from(bool $allowAllSubPermissions, ?PermissionValue $value = null): self
    {
        return new self($allowAllSubPermissions, $value);
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (bool) $data['allow_all_sub_permissions'],
            isset($data['value']) ? PermissionValue::from($data['value']) : null
        );
    }

    public function asArray(): array
    {
        return [
            "allow_all_sub_permissions" => $this->allowAllSubPermissions,
            "value" => $this->value?->asInt(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    public function allowAllSubPermissions(): bool
    {
        return $this->allowAllSubPermissions;
    }

    public function hasValue(): bool
    {
        return $this->value !== null;
    }

    public function getValue(): ?PermissionValue
    {
        return $this->value;
    }
}
