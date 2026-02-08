<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim\Middleware;

use Nebalus\Webapi\Config\GeneralConfig;
use Override;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

readonly class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private App $app,
        private GeneralConfig $env
    ) {
    }

    #[Override] public function process(Request $request, RequestHandler $handler): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->withCorsHeaders($this->app->getResponseFactory()->createResponse());
        }

        return $this->withCorsHeaders($handler->handle($request));
    }

    private function withCorsHeaders(Response $response): Response
    {
        if ($response->hasHeader('Content-Type') === false) {
            $response = $response->withHeader('Content-Type', 'application/json');
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $this->env->getAccessControlAllowOrigin())
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, Accept, Origin')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
