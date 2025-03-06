<?php

namespace Nebalus\Webapi\Factory;

use Nebalus\Webapi\Option\EnvData;
use Nebalus\Webapi\Option\RedisEnv;
use Redis;

readonly class RedisFactory
{
    public function __construct(
        private RedisEnv $redisEnv
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
