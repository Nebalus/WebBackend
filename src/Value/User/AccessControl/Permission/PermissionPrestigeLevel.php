<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

readonly class PermissionPrestigeLevel
{
    private function __construct(
        private int $level,
        private string $name,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(string $prestigeLevel): self
    {
        $prestigeLevel = strtoupper($prestigeLevel);

        return match ($prestigeLevel) {
            "CRITICAL" => new self(1, "CRITICAL"),
            "HIGH" => new self(2, "HIGH"),
            "MODERATE" => new self(3, "MODERATE"),
            "LOW" => new self(4, "LOW"),
            default => throw new ApiInvalidArgumentException('Invalid Permission Prestige Level: ' . $prestigeLevel),
        };
    }

    public function asInt(): int
    {
        return $this->level;
    }

    public function asString(): string
    {
        return $this->name;
    }
}
