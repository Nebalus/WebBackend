<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Verify;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Repository\VerificationTokenRepository\RedisVerificationTokenRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserId;

readonly class VerifyChangeEmailService
{
    private const string TOKEN_TYPE = 'email_change';

    public function __construct(
        private MySqlUserRepository $userRepository,
        private RedisVerificationTokenRepository $tokenRepository,
        private VerifyChangeEmailResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(VerifyChangeEmailValidator $validator): ResultInterface
    {
        $tokenData = $this->tokenRepository->getTokenData(self::TOKEN_TYPE, $validator->getToken());

        if ($tokenData === null) {
            return Result::createError('Invalid or expired verification token', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $userId = UserId::from($tokenData['user_id']);
        $newEmail = UserEmail::from($tokenData['new_email']);

        $existingUser = $this->userRepository->findUserFromEmail($newEmail);
        if ($existingUser !== null) {
            $this->tokenRepository->deleteToken(self::TOKEN_TYPE, $validator->getToken());
            return Result::createError('Email is already in use', StatusCodeInterface::STATUS_CONFLICT);
        }

        $updated = $this->userRepository->updateEmail($userId, $newEmail);
        if (!$updated) {
            return Result::createError('Failed to update email', StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $this->tokenRepository->deleteToken(self::TOKEN_TYPE, $validator->getToken());

        return $this->responder->render($newEmail);
    }
}
