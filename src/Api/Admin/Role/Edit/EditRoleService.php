<?php

namespace Nebalus\Webapi\Api\Admin\Role\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodesTypes;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\RoleRepository\MySqlRoleRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class EditRoleService
{
    public function __construct(
        private MySQlRoleRepository $roleRepository,
        private EditRoleResponder $responder,
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function execute(EditRoleValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::ADMIN_ROLE_DELETE, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $role = $this->roleRepository->findRoleByRoleId($validator->getRoleId());

        if ($role === null) {
            return Result::createError('Role does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($role->isEditable() === false) {
            return Result::createError('This role cannot be edited', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        return Result::createError('PLACEHOLDER', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
