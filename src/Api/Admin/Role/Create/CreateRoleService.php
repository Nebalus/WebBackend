<?php

namespace Nebalus\Webapi\Api\Admin\Role\Create;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;

readonly class CreateRoleService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private CreateRoleResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(CreateRoleValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE_CREATE, true))) {
            $role = Role::create($validator->getRoleName(), $validator->getRoleDescription(), $validator->getRoleColor(), $validator->getAccessLevel(), $validator->appliesToEveryone(), $validator->isDisabled());
            $role = $this->roleRepository->insertRole($role);

            return $this->responder->render($role);
        }

        return ResultBuilder::buildNoPermissionResult();
    }
}
