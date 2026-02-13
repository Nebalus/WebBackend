<?php

namespace Nebalus\Webapi\Slim\Middleware;

use Override;
use Prometheus\CollectorRegistry;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Throwable;

readonly class MetricsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private CollectorRegistry $metricRegistry,
    ) {
    }

    #[Override] public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        try {
            $routeContext = RouteContext::fromRequest($request);
            $route = $routeContext->getRoute();
            $this->metricRegistry->getOrRegisterCounter(
                "backend",
                "api_endpoint",
                "Total number of requests handled by the API",
                ["scheme", "method", "path", "code"]
            )->inc([
                $request->getUri()->getScheme(),
                $request->getMethod(),
                $route->getPattern(),
                $response->getStatusCode()
            ]);
        } catch (Throwable $exception) {
            echo $exception->getMessage();
        }

        return $response;
    }
}
