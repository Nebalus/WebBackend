<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Factory;

use Nebalus\Webapi\Config\MySqlConfig;
use PDO;

readonly class PdoFactory
{
    public function __construct(
        private MySqlConfig $mySqlEnv
    ) {
    }

    public function __invoke(): Pdo
    {
        $host = $this->mySqlEnv->getMySqlHost();
        $port = $this->mySqlEnv->getMySqlPort();
        $database = $this->mySqlEnv->getMySqlDatabase();
        $username = $this->mySqlEnv->getMySqlUser();
        $password = $this->mySqlEnv->getMySqlPasswd();

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $host, $port, $database);
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        return $pdo;
    }
}
