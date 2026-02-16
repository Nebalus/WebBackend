<?php

namespace Nebalus\Webapi\Api\Admin\User\Edit;

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

readonly class EditUserService
{
    public function __construct(
        private MySqlUserRepository $userRepository,
        private EditUserResponder $responder,
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function execute(EditUserValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_USER_EDIT, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $user = $this->userRepository->findUserFromId($validator->getUserId());

        if ($user === null) {
            return Result::createError('User not found', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->userRepository->updateUsername($validator->getUserId(), $validator->getUsername());
        $this->userRepository->updateEmail($validator->getUserId(), $validator->getEmail());
        $this->userRepository->updateEmailVerified($validator->getUserId(), $validator->isEmailVerified());

        $updatedUser = $this->userRepository->findUserFromId($validator->getUserId());

        return $this->responder->render($updatedUser);
    }
}
