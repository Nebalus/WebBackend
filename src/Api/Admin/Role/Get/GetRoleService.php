<?php

namespace Nebalus\Webapi\Api\Admin\Role\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetRoleService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private GetRoleResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function execute(GetRoleValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE, true))) {
            $role = $this->roleRepository->findRoleByRoleId($validator->getRoleId());

            if ($role === null) {
                return Result::createError("Role not found", StatusCodeInterface::STATUS_NOT_FOUND);
            }

            return $this->responder->render($role);
        }

        return ResultBuilder::buildNoPermissionResult();
    }
}
