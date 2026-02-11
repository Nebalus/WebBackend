<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim\Middleware;

use Override;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

readonly class SecurityHeadersMiddleware implements MiddlewareInterface
{
    #[Override] public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        return $response
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->withHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
