<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Request;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Monolog\Logger;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Repository\VerificationTokenRepository\RedisVerificationTokenRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Resend\Client as ResendClient;
use Twig\Environment as TwigEnvironment;

readonly class RequestChangeEmailService
{
    private const string TOKEN_TYPE = 'email_change';

    public function __construct(
        private MySqlUserRepository $userRepository,
        private RedisVerificationTokenRepository $tokenRepository,
        private RequestChangeEmailResponder $responder,
        private ResendClient $resendClient,
        private TwigEnvironment $twig,
        private Logger $logger,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(RequestChangeEmailValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if (!$isSelfUser || !$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_ACCOUNT_OWN_CHANGE_EMAIL, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $existingUser = $this->userRepository->findUserFromEmail($validator->getEmail());
        if ($existingUser !== null) {
            return Result::createError('Email is already in use', StatusCodeInterface::STATUS_CONFLICT);
        }

        $token = bin2hex(random_bytes(32));
        $this->tokenRepository->storeToken(self::TOKEN_TYPE, $token, [
            'user_id' => $requestingUser->getUserId()->asInt(),
            'new_email' => $validator->getEmail()->asString(),
        ]);

        try {
            $this->resendClient->emails->send([
                'from' => 'noreply@nebalus.dev',
                'to' => $validator->getEmail()->asString(),
                'subject' => 'Email Change Verification',
                'html' => $this->twig->render('/email/email_change_verification.twig', [
                    'username' => $requestingUser->getUsername()->asString(),
                    'token' => $token,
                ]),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this->responder->render();
    }
}
