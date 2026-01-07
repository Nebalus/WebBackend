<?php

namespace Nebalus\Webapi;

use DI\Definition\Source\DefinitionArray;
use Exception;
use Monolog\Logger;
use Nebalus\Webapi\Factory\LoggerFactory;
use Nebalus\Webapi\Factory\MetricCollectorRegistryFactory;
use Nebalus\Webapi\Factory\PdoFactory;
use Nebalus\Webapi\Factory\RedisFactory;
use Nebalus\Webapi\Factory\ResendFactory;
use Nebalus\Webapi\Factory\TwigFactory;
use PDO;
use Prometheus\CollectorRegistry;
use Redis;
use Resend\Client as ResendClient;
use Twig\Environment as TwigEnvironment;
use function DI\factory;

class ApplicationConfig extends DefinitionArray
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct($this->getConfig());
    }

    private function getConfig(): array
    {
        return [
            PDO::class => factory(PdoFactory::class),
            Logger::class => factory(LoggerFactory::class),
            Redis::class => factory(RedisFactory::class),
            CollectorRegistry::class => factory(MetricCollectorRegistryFactory::class),
            ResendClient::class => factory(ResendFactory::class),
            TwigEnvironment::class => factory(TwigFactory::class),
        ];
    }
}
