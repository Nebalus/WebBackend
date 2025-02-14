<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\Auth;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Option\EnvData;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use ReallySimpleJWT\Exception\BuildException;
use ReallySimpleJWT\Token;

readonly class AuthUserService
{
    public function __construct(
        private MySqlUserRepository $mySqlUserRepository,
        private EnvData $envData
    ) {
    }

    /**
     * @throws ApiException|BuildException
     */
    public function execute(AuthUserValidator $validator): ResultInterface
    {
        $user = $this->mySqlUserRepository->findUserFromUsername($validator->getUsername());

        if ($user === null || $user->isDisabled() || $user->getPassword()->verify($validator->getPassword()) === false) {
            return Result::createError('Authentication failed: Wrong credentials', 401);
        }

        $expirationTime = time() + $this->envData->getJwtNormalExpirationTime();

        $token = Token::builder($this->envData->getJwtSecret())
            ->setIssuer("https://api.nebalus.dev")
            ->setPayloadClaim("email", $user->getEmail()->asString())
            ->setPayloadClaim("username", $user->getUsername()->asString())
            ->setPayloadClaim("sub", $user->getUserId()?->asInt())
            ->setIssuedAt(time())
            ->setExpiration($expirationTime)
            ->build();

        return AuthUserView::render($token, $user);
    }
}
