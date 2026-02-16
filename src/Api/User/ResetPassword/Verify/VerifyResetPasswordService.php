<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ResetPassword\Verify;

use DateTimeImmutable;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Monolog\Logger;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Repository\VerificationTokenRepository\RedisVerificationTokenRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use Nebalus\Webapi\Value\User\UserId;
use Resend\Client as ResendClient;
use Twig\Environment as TwigEnvironment;

readonly class VerifyResetPasswordService
{
    private const string TOKEN_TYPE = 'password_reset';

    public function __construct(
        private MySqlUserRepository $userRepository,
        private RedisVerificationTokenRepository $tokenRepository,
        private VerifyResetPasswordResponder $responder,
        private ResendClient $resendClient,
        private TwigEnvironment $twig,
        private Logger $logger,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(VerifyResetPasswordValidator $validator, string $clientIp): ResultInterface
    {
        $tokenData = $this->tokenRepository->getTokenData(self::TOKEN_TYPE, $validator->getToken());

        if ($tokenData === null) {
            return Result::createError('Invalid or expired reset token', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $userId = UserId::from($tokenData['user_id']);
        $user = $this->userRepository->findUserFromId($userId);

        if ($user === null || $user->isDisabled()) {
            $this->tokenRepository->deleteToken(self::TOKEN_TYPE, $validator->getToken());
            return Result::createError('Invalid or expired reset token', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $newPassword = UserPassword::fromPlain($validator->getNewPassword());
        $updated = $this->userRepository->updatePassword($userId, $newPassword);

        if (!$updated) {
            return Result::createError('Failed to reset password', StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $this->tokenRepository->deleteToken(self::TOKEN_TYPE, $validator->getToken());

        try {
            $currentDateTime = new DateTimeImmutable();
            $this->resendClient->emails->send([
                'from' => 'noreply@nebalus.dev',
                'to' => $user->getEmail()->asString(),
                'subject' => 'Password Changed',
                'html' => $this->twig->render('/email/password_changed.twig', [
                    'username' => $user->getUsername()->asString(),
                    'ip_address' => $clientIp,
                    'change_time' => $currentDateTime->format('Y-m-d H:i:s'),
                ]),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this->responder->render();
    }
}
