<?php

namespace Nebalus\Webapi\Api\Admin\Role\GetAll;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetAllRoleService
{
    public function __construct(
        private GetAllRoleResponder $responder,
        private MySqlRoleRepository $roleRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE, true))) {
            $roles = $this->roleRepository->getAllRoles();
            return $this->responder->render($roles);
        }
        return ResultBuilder::buildNoPermissionResult();
    }
}
