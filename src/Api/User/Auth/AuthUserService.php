<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\Auth;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\GeneralConfig;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use ReallySimpleJWT\Exception\BuildException;
use ReallySimpleJWT\Token;
use Resend\Client as ResendClient;

readonly class AuthUserService
{
    public function __construct(
        private MySqlUserRepository $mySqlUserRepository,
        private GeneralConfig $generalConfig,
        private AuthUserResponder $responder,
        private ResendClient $resendClient,
    ) {
    }

    /**
     * @throws ApiException|BuildException
     */
    public function execute(AuthUserValidator $validator): ResultInterface
    {
        $user = $this->mySqlUserRepository->findUserFromUsername($validator->getUsername());

        if ($user === null || $user->isDisabled() || $user->getPassword()->verify($validator->getPassword()) === false) {
            return Result::createError('Authentication failed: Wrong credentials', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        $expirationTime = time() + $this->generalConfig->getJwtExpirationTime();

        $this->resendClient->emails->send([
            'from' => 'noreply@nebalus.dev',
            'to' => 'contact@nebalus.dev',
            'subject' => 'Hello Und Willkommen',
            'html' => '<strong>...</strong>',
        ]);

        $token = Token::builder($this->generalConfig->getJwtSecret())
            ->setIssuer("https://api.nebalus.dev")
            ->setPayloadClaim("email", $user->getEmail()->asString())
            ->setPayloadClaim("username", $user->getUsername()->asString())
            ->setPayloadClaim("sub", $user->getUserId()?->asInt())
            ->setIssuedAt(time())
            ->setExpiration($expirationTime)
            ->build();

        return $this->responder->render($token, $user);
    }
}
