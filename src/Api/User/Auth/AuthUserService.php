<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\Auth;

use DateTimeImmutable;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Monolog\Logger;
use Nebalus\Webapi\Config\GeneralConfig;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Utils\IpUtils;
use Nebalus\Webapi\Value\Result\Result;
use ReallySimpleJWT\Exception\BuildException;
use ReallySimpleJWT\Token;
use Resend\Client as ResendClient;
use Twig\Environment as TwigEnvironment;

readonly class AuthUserService
{
    public function __construct(
        private MySqlUserRepository $mySqlUserRepository,
        private GeneralConfig $generalConfig,
        private AuthUserResponder $responder,
        private ResendClient $resendClient,
        private TwigEnvironment $twig,
        private IpUtils $ipUtils,
        private Logger $logger,
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

        try {
            $currentDateTime = new DateTimeImmutable();
            $this->resendClient->emails->send([
                'from' => 'noreply@nebalus.dev',
                'to' => $user->getEmail()->asString(),
                'subject' => 'Login Confirmation',
                'html' => $this->twig->render("/email/user_login.twig", [
                    "username" => $user->getUsername()->asString(),
                    "ip_address" => $this->ipUtils->getClientIP(),
                    "login_time" => $currentDateTime->format("Y-m-d H:i:s"),
                ]),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $expirationTime = time() + $this->generalConfig->getJwtExpirationTime();
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
