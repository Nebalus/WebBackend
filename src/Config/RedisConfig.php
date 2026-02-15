<?php

namespace Nebalus\Webapi\Config;

class RedisConfig
{
    private bool $isRedisConfigured;
    private string $redisHost;
    private string $redisPort;

    public function __construct()
    {
        $redisHost = getenv("REDIS_HOST");
        $redisPort = getenv("REDIS_PORT");

        if ($redisHost === false || $redisPort === false) {
            $this->isRedisConfigured = false;
            $this->redisHost = $redisHost;
            $this->redisPort = $redisPort;
            return;
        }

        $this->redisHost = $redisHost;
        $this->redisPort = $redisPort;
    }

    public function isRedisConfigured(): bool
    {
        return $this->isRedisConfigured;
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
