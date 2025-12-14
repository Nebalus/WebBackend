<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\Add;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\User;

readonly class AddRoleToUserService
{
    public function __construct(
        private MySqlUserRepository $userRepository,
        private MySqlRoleRepository $roleRepository,
        private AddRoleToUserResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(AddRoleToUserValidator $validator, User $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_USER_ROLE_ADD, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $roleThatWantsToBeAdded = $this->roleRepository->findRoleByRoleId($validator->getRoleId());

        if ($roleThatWantsToBeAdded === null) {
            return Result::createError("The role you want add to does not exist under this id", StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $unsortedRolesOfRequestingUserArray = $this->userRepository->getAllRolesFromUserByUserId($requestingUser->getUserId())->toArray();
        usort($unsortedRolesOfRequestingUserArray, function (Role $a, Role $b) {
            return $a->getAccessLevel()->asInt() <=> $b->getAccessLevel()->asInt();
        });

        $highestRoleOfRequestingUser = $unsortedRolesOfRequestingUserArray[0] ?? null;
        if ($highestRoleOfRequestingUser === null) {
            return Result::createError("This user needs to have at least one role (How did we even get here)", StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($highestRoleOfRequestingUser->getAccessLevel()->asInt() >= $roleThatWantsToBeAdded->getAccessLevel()->asInt()) {
            return Result::createError("You cannot add this role to this user because this role is above your highest role", StatusCodeInterface::STATUS_FORBIDDEN);
        }

        if ($this->userRepository->insertRoleToUserByRoleId($validator->getUserId(), $validator->getRoleId())) {
            return $this->responder->render($roleThatWantsToBeAdded);
        }

        return Result::createError('No changes were made to the users roles', StatusCodeInterface::STATUS_BAD_REQUEST);
    }
}
