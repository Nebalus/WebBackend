<?php

namespace Nebalus\Webapi\Api\Admin\User\Delete;

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

readonly class DeleteUserService
{
    public function __construct(
        private MySqlUserRepository $userRepository,
        private DeleteUserResponder $responder,
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function execute(DeleteUserValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_USER_DELETE, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $user = $this->userRepository->findUserFromId($validator->getUserId());

        if ($user === null) {
            return Result::createError('User not found', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($this->userRepository->deleteUser($validator->getUserId())) {
            return $this->responder->render();
        }

        return Result::createError('Failed to delete user', StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
    }
}
