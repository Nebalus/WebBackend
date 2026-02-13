<?php

namespace Nebalus\Webapi\Factory;

use Prometheus\CollectorRegistry;
use Prometheus\Exception\StorageException;
use Prometheus\Storage\Redis as RedisAdapter;
use Redis;

readonly class MetricCollectorRegistryFactory
{
    public function __construct(
        private Redis $redis,
    ) {
    }

    /**
     * @throws StorageException
     */
    public function __invoke(): CollectorRegistry
    {
        $redisAdapter = RedisAdapter::fromExistingConnection($this->redis);
        return new CollectorRegistry($redisAdapter);
    }
}
