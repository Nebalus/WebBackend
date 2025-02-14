<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim\Middleware;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Option\EnvData;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\User\UserId;
use Override;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use ReallySimpleJWT\Token;
use Slim\App;

readonly class AuthenticationMiddleware implements MiddlewareInterface
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
        if ($request->hasHeader("Authorization") === false) {
            return $this->abort("The Authorization header is not provided", 401);
        }

        $jwtParser = Token::parser($request->getHeader("Authorization")[0]);
        $jwt = $jwtParser->parse();
        $token = $jwt->getJwt()->getToken();
        $payload = $jwt->getPayload();

        if (!Token::validate($token, $this->env->getJwtSecret())) {
            return $this->abort("The JWT is not valid", 401);
        }

        $userId = UserId::from($payload["sub"]);
        $user = $this->userRepository->findUserFromId(($userId));

        if (
            $user === null ||
            $user->isDisabled() ||
            $jwt->getIssuedAt() < $user->getUpdatedAtDate()->getTimestamp() ||
            Token::validateExpiration($token) === false
        ) {
            return $this->abort("The JWT has expired", 401);
        }

        $request = $request->withAttribute("user", $user);

        return $handler->handle($request);
    }

    private function abort(string $errorMessage, int $code): Response
    {
        $apiResponse = Result::createError($errorMessage, $code);
        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write($apiResponse->getPayloadAsJson());
        return $response;
    }
}
