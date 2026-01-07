<?php

namespace Nebalus\Webapi\Factory;

use Nebalus\Webapi\Config\RedisConfig;
use Redis;

readonly class RedisFactory
{
    public function __construct(
        private RedisConfig $redisEnv
    ) {
    }

    public function __invoke(): Redis
    {
        $options = [
            'readTimeout' => 2.5,
            'connectTimeout' => 2.5,
            'persistent' => false,
            'retryInterval' => 5,
        ];
        $redis = new Redis($options);
        $redis->connect($this->redisEnv->getRedisHost(), $this->redisEnv->getRedisPort());

        return $redis;
    }
}
