<?php

namespace Nebalus\Webapi\Config;

class MySqlConfig
{
    use ConfigTrait;

    private string $mysqlPasswd;
    private string $mysqlHost;
    private string $mysqlPort;
    private string $mysqlDatabase;
    private string $mysqlUser;

    public function __construct()
    {
        $this->mysqlPasswd = self::requireEnv("MYSQL_PASSWORD");
        $this->mysqlHost = self::requireEnv("MYSQL_HOST");
        $this->mysqlPort = self::requireEnv("MYSQL_PORT");
        $this->mysqlDatabase = self::requireEnv("MYSQL_DATABASE");
        $this->mysqlUser = self::requireEnv("MYSQL_USER");
    }

    public function getMySqlPasswd(): string
    {
        return $this->mysqlPasswd;
    }

    public function getMySqlHost(): string
    {
        return $this->mysqlHost;
    }

    public function getMySqlPort(): string
    {
        return $this->mysqlPort;
    }

    public function getMySqlDatabase(): string
    {
        return $this->mysqlDatabase;
    }

    public function getMySqlUser(): string
    {
        return $this->mysqlUser;
    }
}
