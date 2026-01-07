<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Role;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

readonly class Role
{
    private function __construct(
        private ?RoleId $roleId,
        private RoleName $roleName,
        private ?RoleDescription $roleDescription,
        private RoleHexColor $roleColor,
        private RoleAccessLevel $accessLevel,
        private bool $appliesToEveryone,
        private bool $deletable,
        private bool $editable,
        private bool $disabled,
        private DateTimeImmutable $createdAtDate,
        private DateTimeImmutable $updatedAtDate,
    ) {
    }

    public static function create(
        RoleName $roleName,
        ?RoleDescription $roleDescription,
        RoleHexColor $roleColor,
        RoleAccessLevel $accessLevel,
        bool $appliesToEveryone,
        bool $disabled,
    ): self {
        $createdAtDate = new DateTimeImmutable();
        $updatedAtDate = new DateTimeImmutable();
        return self::from(null, $roleName, $roleDescription, $roleColor, $accessLevel, $appliesToEveryone, true, true, $disabled, $createdAtDate, $updatedAtDate);
    }

    public static function from(
        ?RoleId $roleId,
        RoleName $roleName,
        ?RoleDescription $roleDescription,
        RoleHexColor $roleColor,
        RoleAccessLevel $accessLevel,
        bool $appliesToEveryone,
        bool $deletable,
        bool $editable,
        bool $disabled,
        DateTimeImmutable $createdAtDate,
        DateTimeImmutable $updatedAtDate
    ): self {
        return new self(
            $roleId,
            $roleName,
            $roleDescription,
            $roleColor,
            $accessLevel,
            $appliesToEveryone,
            $deletable,
            $editable,
            $disabled,
            $createdAtDate,
            $updatedAtDate
        );
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiDateMalformedStringException
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data['created_at']);
            $updatedAtDate = new DateTimeImmutable($data['updated_at']);
            return new self(
                empty($data['role_id']) ? null : RoleId::from($data['role_id']),
                RoleName::from($data['name']),
                empty($data['description']) ? null : RoleDescription::from($data['description']),
                RoleHexColor::from($data['color']),
                RoleAccessLevel::from($data['access_level']),
                (bool) $data['applies_to_everyone'],
                (bool) $data['deletable'],
                (bool) $data['editable'],
                (bool) $data['disabled'],
                $createdAtDate,
                $updatedAtDate
            );
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }
    }

    public function asArray(): array
    {
        return [
            'role_id' => $this->roleId?->asInt(),
            'name' => $this->roleName->asString(),
            'description' => $this->roleDescription?->asString(),
            'color' => $this->roleColor->asString(),
            'access_level' => $this->accessLevel->asInt(),
            'applies_to_everyone' => $this->appliesToEveryone,
            'deletable' => $this->deletable,
            'editable' => $this->editable,
            'disabled' => $this->disabled,
            'created_at' => $this->createdAtDate->format(DATE_ATOM),
            'updated_at' => $this->updatedAtDate->format(DATE_ATOM),
        ];
    }

    public function getRoleId(): ?RoleId
    {
        return $this->roleId;
    }

    public function getName(): RoleName
    {
        return $this->roleName;
    }

    public function getDescription(): ?RoleDescription
    {
        return $this->roleDescription;
    }

    public function getColor(): RoleHexColor
    {
        return $this->roleColor;
    }

    public function getAccessLevel(): RoleAccessLevel
    {
        return $this->accessLevel;
    }

    public function appliesToEveryone(): bool
    {
        return $this->appliesToEveryone;
    }

    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getCreatedAtDate(): DateTimeImmutable
    {
        return $this->createdAtDate;
    }

    public function getUpdatedAtDate(): DateTimeImmutable
    {
        return $this->updatedAtDate;
    }
}
