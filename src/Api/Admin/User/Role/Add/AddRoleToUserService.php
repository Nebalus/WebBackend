<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\Add;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class AddRoleToUserService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private AddRoleToUserResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(AddRoleToUserValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_USER_ROLE_ADD, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        return $this->responder->render();
    }
}
