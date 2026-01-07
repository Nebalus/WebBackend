<?php

namespace Nebalus\Webapi\Config;

class MySqlConfig
{
    private string $mysqlPasswd;
    private string $mysqlHost;
    private string $mysqlPort;
    private string $mysqlDatabase;
    private string $mysqlUser;

    public function __construct()
    {
        $this->mysqlPasswd = getenv("MYSQL_PASSWORD");
        $this->mysqlHost = getenv("MYSQL_HOST");
        $this->mysqlPort = getenv("MYSQL_PORT");
        $this->mysqlDatabase = getenv("MYSQL_DATABASE");
        $this->mysqlUser = getenv("MYSQL_USER");
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
