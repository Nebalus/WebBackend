<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim\Middleware;

use Closure;
use DI\Container;
use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Value\Result\Result;
use Override;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Redis;
use Slim\App;

readonly class RateLimitMiddleware implements MiddlewareInterface
{
    private const int DEFAULT_MAX_REQUESTS = 10;
    private const int DEFAULT_WINDOW_SECONDS = 180;
    private const string KEY_PREFIX = 'rate_limit:';

    public function __construct(
        private App $app,
        private Redis $redis,
        private int $maxRequests = self::DEFAULT_MAX_REQUESTS,
        private int $windowSeconds = self::DEFAULT_WINDOW_SECONDS,
    ) {
    }

    public static function withConfig(int $maxRequests, int $windowSeconds): Closure
    {
        return function (Container $container) use ($maxRequests, $windowSeconds): self {
            return new self(
                $container->get(App::class),
                $container->get(Redis::class),
                $maxRequests,
                $windowSeconds,
            );
        };
    }

    #[Override] public function process(Request $request, RequestHandler $handler): Response
    {
        $clientIp = $request->getAttribute(AttributeTypes::CLIENT_IP);
        $path = $request->getUri()->getPath();
        $key = self::KEY_PREFIX . $clientIp . ':' . $path;

        try {
            $count = $this->redis->incr($key);

            if ($count === 1) {
                $this->redis->expire($key, $this->windowSeconds);
            }

            if ($count > $this->maxRequests) {
                $ttl = $this->redis->ttl($key);
                return $this->createRateLimitResponse($ttl > 0 ? $ttl : $this->windowSeconds);
            }
        } catch (\Throwable) {
            // If Redis is unavailable, allow the request through
        }

        return $handler->handle($request);
    }

    private function createRateLimitResponse(int $retryAfter): Response
    {
        $result = Result::createError(
            'Too many requests. Please try again later.',
            StatusCodeInterface::STATUS_TOO_MANY_REQUESTS
        );

        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write($result->getPayloadAsJson());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Retry-After', (string) $retryAfter)
            ->withHeader('X-RateLimit-Limit', (string) $this->maxRequests)
            ->withHeader('X-RateLimit-Reset', (string) $retryAfter)
            ->withStatus(StatusCodeInterface::STATUS_TOO_MANY_REQUESTS);
    }
}
