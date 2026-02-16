<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ResetPassword\Request;

use Exception;
use Monolog\Logger;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Repository\VerificationTokenRepository\RedisVerificationTokenRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Resend\Client as ResendClient;
use Twig\Environment as TwigEnvironment;

readonly class RequestResetPasswordService
{
    private const string TOKEN_TYPE = 'password_reset';

    public function __construct(
        private MySqlUserRepository $userRepository,
        private RedisVerificationTokenRepository $tokenRepository,
        private RequestResetPasswordResponder $responder,
        private ResendClient $resendClient,
        private TwigEnvironment $twig,
        private Logger $logger,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(RequestResetPasswordValidator $validator): ResultInterface
    {
        $user = $this->userRepository->findUserFromEmail($validator->getEmail());

        // Always return the same generic success response to prevent email enumeration
        if ($user !== null && !$user->isDisabled()) {
            $token = bin2hex(random_bytes(32));
            $this->tokenRepository->storeToken(self::TOKEN_TYPE, $token, [
                'user_id' => $user->getUserId()->asInt(),
            ]);

            try {
                $this->resendClient->emails->send([
                    'from' => 'noreply@nebalus.dev',
                    'to' => $user->getEmail()->asString(),
                    'subject' => 'Password Reset Request',
                    'html' => $this->twig->render('/email/password_reset_request.twig', [
                        'username' => $user->getUsername()->asString(),
                        'token' => $token,
                    ]),
                ]);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $this->responder->render();
    }
}
