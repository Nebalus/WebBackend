<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Config;

use Monolog\Level;

class GeneralConfig
{
    use ConfigTrait;

    private bool $isProduction;
    private bool $isDevelopment;
    private Level $logLevel;
    private string $jwtSecret;
    private string $accessControlAllowOrigin;
    private int $jwtExpirationTime;

    public function __construct()
    {
        $appEnv = strtolower(self::requireEnv("APP_ENV"));
        $this->isProduction = $appEnv === "production";
        $this->isDevelopment = $appEnv === "development";
        $this->logLevel = Level::fromName(self::requireEnv("ERROR_LOGLEVEL"));
        $this->jwtSecret = self::requireEnv("JWT_SECRET");
        $this->jwtExpirationTime = (int) self::requireEnv('JWT_EXPIRATION_TIME');
        $this->accessControlAllowOrigin = self::requireEnv("ACCESS_CONTROL_ALLOW_ORIGIN");
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
