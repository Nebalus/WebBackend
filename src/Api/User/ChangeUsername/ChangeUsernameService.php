<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeUsername;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;

readonly class ChangeUsernameService
{
    public function __construct(
        private MySqlUserRepository $userRepository,
        private ChangeUsernameResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(ChangeUsernameValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if (!$isSelfUser || !$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_ACCOUNT_OWN_CHANGE_USERNAME, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $existingUser = $this->userRepository->findUserFromUsername($validator->getUsername());
        if ($existingUser !== null) {
            return Result::createError('Username is already taken', StatusCodeInterface::STATUS_CONFLICT);
        }

        $updated = $this->userRepository->updateUsername($requestingUser->getUserId(), $validator->getUsername());
        if (!$updated) {
            return Result::createError('Failed to update username', StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        return $this->responder->render($validator->getUsername());
    }
}
