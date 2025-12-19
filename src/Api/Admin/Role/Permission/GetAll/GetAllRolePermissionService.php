<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\GetAll;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetAllRolePermissionService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private GetAllRolePermissionResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function execute(GetAllRolePermissionValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE_EDIT, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $permissions = $this->roleRepository->getAllPermissionLinksByRoleId($validator->getRoleId());
        return $this->responder->render($permissions);
    }
}
