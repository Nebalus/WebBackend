<?php

namespace Nebalus\Webapi\Api\Admin\User\Disable;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;

readonly class DisableUserService
{
    public function __construct(
        private MySqlUserRepository $userRepository,
        private DisableUserResponder $responder,
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function execute(DisableUserValidator $validator, UserPermissionIndex $userPerms, UserAccount $clientUser): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_USER_DISABLE, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $user = $this->userRepository->findUserFromId($validator->getUserId());

        if ($user === null) {
            return Result::createError('User not found', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($validator->isDisabled()) {
            $reason = $validator->getReason() ?? '';
            $this->userRepository->disableUser($validator->getUserId(), $clientUser->getUserId(), $reason);
        } else {
            $this->userRepository->enableUser($validator->getUserId());
        }

        $updatedUser = $this->userRepository->findUserFromId($validator->getUserId());

        return $this->responder->render($updatedUser);
    }
}
