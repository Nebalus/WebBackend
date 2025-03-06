<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Option\EnvData;
use Nebalus\Webapi\Repository\User\MySqlUserRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\User\UserId;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use ReallySimpleJWT\Token;
use Slim\App;

readonly class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private App $app,
        private MySqlUserRepository $userRepository,
        private EnvData $env
    ) {
    }

    /**
     * @throws ApiException
     */
    #[Override] public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)) {
            return $this->denyRequest('Missing "Authorization" header');
        }

        return $this->processJwt($authHeader[0], $request, $handler);
    }

    /**
     * @throws ApiException
     */
    private function processJwt(
        string $jwt,
        Request $request,
        RequestHandler $handler
    ): Response {
        if (empty($jwt)) {
            return $this->denyRequest('Authorization header is empty');
        }

        if (!Token::validate($jwt, $this->env->getJwtSecret())) {
            return $this->denyRequest('The JWT is not valid');
        }

        if (!Token::validateExpiration($jwt)) {
            return $this->denyRequest('The JWT has expired');
        }

        $payloadParsed = Token::parser($jwt)->parse();
        $payload = $payloadParsed->getPayload();
        $userId = UserId::from($payload["sub"]);
        $user = $this->userRepository->findUserFromId(($userId));

        if (
            $user === null ||
            $user->isDisabled() ||
            $payloadParsed->getIssuedAt() < $user->getUpdatedAtDate()->getTimestamp()
        ) {
            return $this->denyRequest("The JWT has expired");
        }

        $request = $request->withAttribute("user", $user);
        $request = $request->withAttribute("authType", "jwt");

        return $handler->handle($request);
    }

    private function denyRequest(string $errorMessage): Response
    {
        $apiResponse = Result::createError($errorMessage, StatusCodeInterface::STATUS_ACCEPTED);
        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write($apiResponse->getPayloadAsJson());
        return $response;
    }
}
