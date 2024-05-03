<?php

namespace Nebalus\Webapi\Option;

use Monolog\Level;
use function DI\get;

class EnvData
{
    private bool $isProduction;
    private bool $isDevelopment;
    private Level $logLevel;
    private string $jwtSecret;
    private string $mysqlPasswd;
    private string $mysqlHost;
    private string $mysqlDbName;
    private string $mysqlUser;

    public function __construct()
    {
        $this->isProduction = strtolower(getenv("APP_ENV")) === "production";
        $this->isDevelopment = strtolower(getenv("APP_ENV")) === "development";
        $this->logLevel = Level::fromName(getenv("ERROR_LOGLEVEL"));
        $this->jwtSecret = getenv("JWT_SECRET");
        $this->mysqlPasswd = getenv("MYSQL_PASSWORD");
        $this->mysqlHost = getenv("MYSQL_HOST");
        $this->mysqlDbName = getenv("MYSQL_DBNAME");
        $this->mysqlUser = getenv("MYSQL_USER");
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

    public function getMySqlPasswd(): string
    {
        return $this->mysqlPasswd;
    }

    public function getMySqlHost(): string
    {
        return $this->mysqlHost;
    }

    public function getMySqlDbName(): string
    {
        return $this->mysqlDbName;
    }

    public function getMySqlUser(): string
    {
        return $this->mysqlUser;
    }
}