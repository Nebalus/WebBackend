<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Config;

use Monolog\Level;

class GeneralConfig
{
    private bool $isProduction;
    private bool $isDevelopment;
    private Level $logLevel;
    private string $jwtSecret;
    private string $accessControlAllowOrigin;
    private int $jwtExpirationTime;

    public function __construct()
    {
        $this->isProduction = strtolower(getenv("APP_ENV")) === "production";
        $this->isDevelopment = strtolower(getenv("APP_ENV")) === "development";
        $this->logLevel = Level::fromName(getenv("ERROR_LOGLEVEL"));
        $this->jwtSecret = getenv("JWT_SECRET");
        $this->jwtExpirationTime = (int) getenv('JWT_EXPIRATION_TIME');
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

    public function getJwtExpirationTime(): int
    {
        return $this->jwtExpirationTime;
    }

    public function getAccessControlAllowOrigin(): string
    {
        return $this->accessControlAllowOrigin;
    }
}
