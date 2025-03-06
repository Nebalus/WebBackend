<?php

namespace Nebalus\Webapi\Option;

class RedisEnv
{
    private string $redisHost;
    private string $redisPort;

    public function __construct()
    {
        $this->redisHost = getenv("REDIS_HOST");
        $this->redisPort = getenv("REDIS_PORT");
    }

    public function getRedisHost(): string
    {
        return $this->redisHost;
    }

    public function getRedisPort(): string
    {
        return $this->redisPort;
    }
}
