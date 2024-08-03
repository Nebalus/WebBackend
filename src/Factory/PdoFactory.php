<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Factory;

use Nebalus\Webapi\Option\EnvData;
use PDO;
use PDOException;

class PdoFactory
{
    private EnvData $env;
    public function __construct(EnvData $env)
    {
        $this->env = $env;
    }

    /**
     * @throws PDOException
     */
    public function build(): Pdo
    {
        $dsn = 'mysql:host=' . $this->env->getMySqlHost() . ';dbname=' . $this->env->getMySqlDbName();

        return new Pdo($dsn, $this->env->getMySqlUser(), $this->env->getMySqlPasswd());
    }
}
