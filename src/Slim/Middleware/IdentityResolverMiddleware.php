<?php

namespace Nebalus\Webapi\Slim\Middleware;

use Exception;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Utils\CryptUtils;
use Nebalus\Webapi\Utils\IpUtils;
use Override;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

readonly class IdentityResolverMiddleware implements MiddlewareInterface
{
    public function __construct(
        private IpUtils $ipUtils,
        private CryptUtils $cryptUtils,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function process(Request $request, RequestHandler $handler): Response
    {
        $clientIp = $this->ipUtils->getClientIp($request);
        $userAgent = $request->getHeaderLine('User-Agent') ?: 'unknown';

        $request = $request->withAttribute(AttributeTypes::CLIENT_IP, $clientIp);
        $request = $request->withAttribute(AttributeTypes::CLIENT_USER_AGENT, $userAgent);
        $request = $request->withAttribute(AttributeTypes::CLIENT_ANONYMOUS_IDENTITY_HASH, $this->cryptUtils->generateAnonymousIdentityHash($clientIp, $userAgent));

        return $handler->handle($request);
    }
}
