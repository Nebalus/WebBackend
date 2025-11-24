<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\GeneralConfig;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserId;
use Override;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use ReallySimpleJWT\Token;
use Slim\App;

readonly class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private App $app,
        private MySqlUserRepository $userRepository,
        private GeneralConfig $env
    ) {
    }

    /**
     * @throws ApiException
     */
    #[Override] public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)) {
            return $this->denyRequest("Missing 'Authorization' header");
        }

        $headerSplit = explode(" ", $authHeader[0]);
        $headerComponentCount = count($headerSplit);

        if ($headerComponentCount !== 2) {
            return $this->denyRequest("Invalid 'Authorization' header provided");
        }

        if ($headerSplit[0] != "Bearer") {
            return $this->denyRequest("No 'Bearer' JWT provided");
        }

        return $this->processJwt($headerSplit[1], $request, $handler);
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
            return $this->denyRequest('Your provided JWT is not valid');
        }

        if (!Token::validateExpiration($jwt)) {
            return $this->denyRequest('Your provided JWT has expired');
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
            return $this->denyRequest("Your provided JWT has expired");
        }

        $request = $request->withAttribute(AttributeTypes::REQUESTING_USER, $user);
        $request = $request->withAttribute(AttributeTypes::AUTH_TYPE, "jwt");

        return $handler->handle($request);
    }

    private function denyRequest(string $errorMessage): Response
    {
        $apiResponse = Result::createError($errorMessage, StatusCodeInterface::STATUS_UNAUTHORIZED);
        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write($apiResponse->getPayloadAsJson());
        return $response->withStatus($apiResponse->getStatusCode());
    }
}
