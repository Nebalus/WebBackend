<?php

namespace Nebalus\Webapi\Api\User\Register;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Monolog\Logger;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\AccountRepository\MySqlAccountRepository;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\User;
use Resend\Client as ResendClient;
use Twig\Environment as TwigEnvironment;

readonly class RegisterUserService
{
    public function __construct(
        private MySqlUserRepository $mySqlUserRepository,
        private MySqlAccountRepository $mySqlAccountRepository,
        private RegisterUserResponder $responder,
        private ResendClient $resendClient,
        private TwigEnvironment $twig,
        private Logger $logger,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(RegisterUserValidator $validator): ResultInterface
    {
        $invitationToken = $this->mySqlAccountRepository->findInvitationTokenByFields($validator->getPureInvitationToken());

        if ($invitationToken === null) {
            return Result::createError('Registration failed: The Invitation Token you provided does not exist', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        if ($invitationToken->isExpired()) {
            return Result::createError('Registration failed: The Invitation Token you provided is expired', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $userFoundByUsername = $this->mySqlUserRepository->findUserFromUsername($validator->getUsername());

        if ($userFoundByUsername !== null) {
            return Result::createError('Registration failed: The Username you provided is already registered', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $userFoundByEmail = $this->mySqlUserRepository->findUserFromEmail($validator->getUserEmail());

        if ($userFoundByEmail !== null) {
            return Result::createError('Registration failed: The Email you provided is already registered', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $preUser = User::create($validator->getUsername(), $validator->getUserEmail(), $validator->getUserPassword());
        $user = $this->mySqlUserRepository->registerUser($preUser, $invitationToken);

        try {
            $this->resendClient->emails->send([
                'from' => 'noreply@nebalus.dev',
                'to' => $user->getEmail()->asString(),
                'subject' => 'Register Confirmation',
                'html' => $this->twig->render("/email/user_register.twig", [
                    "username" => $user->getUsername()->asString(),
                ]),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this->responder->render();
    }
}
