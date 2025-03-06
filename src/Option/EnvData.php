<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Option;

use Monolog\Level;

class EnvData
{
    private bool $isProduction;
    private bool $isDevelopment;
    private Level $logLevel;
    private string $jwtSecret;
    private string $accessControlAllowOrigin;
    private int $jwtNormalExpirationTime;
    private int $jwtExtendedExpirationTime;

    public function __construct()
    {
        $this->isProduction = strtolower(getenv("APP_ENV")) === "production";
        $this->isDevelopment = strtolower(getenv("APP_ENV")) === "development";
        $this->logLevel = Level::fromName(getenv("ERROR_LOGLEVEL"));
        $this->jwtSecret = getenv("JWT_SECRET");
        $this->jwtNormalExpirationTime = (int) getenv('JWT_NORMAL_EXPIRATION_TIME');
        $this->jwtExtendedExpirationTime = (int) getenv('JWT_EXTENDED_EXPIRATION_TIME');
        $this->accessControlAllowOrigin = getenv("ACCESS_CONTROL_ALLOW_ORIGIN");
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    public function isDevelopment(): bool
    {
        return $this->isDevelopment;
    }

    public function getLogLevel(): Level
    {
        return $this->logLevel;
    }

    public function getJwtSecret(): string
    {
        return $this->jwtSecret;
    }

    public function getJwtNormalExpirationTime(): int
    {
        return $this->jwtNormalExpirationTime;
    }

    public function getJwtExtendedExpirationTime(): int
    {
        return $this->jwtExtendedExpirationTime;
    }
    public function getAccessControlAllowOrigin(): string
    {
        return $this->accessControlAllowOrigin;
    }
}
