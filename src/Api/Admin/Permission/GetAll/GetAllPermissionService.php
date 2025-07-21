<?php

namespace Nebalus\Webapi\Api\Admin\Permission\GetAll;

use Nebalus\Webapi\Config\Types\PermissionNodesTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\PermissionsRepository\MySqlPermissionRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetAllPermissionService
{
    public function __construct(
        private GetAllPermissionResponder $responder,
        private MySqlPermissionRepository $permissionRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::ADMIN_ROLE, true))) {
            $requestedPermissions = $this->permissionRepository->getAllPermissions();

            return $this->responder->render($requestedPermissions);
        }

        return ResultBuilder::buildNoPermissionResult();
    }
}
