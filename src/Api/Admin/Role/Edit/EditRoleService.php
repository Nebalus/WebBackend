<?php

namespace Nebalus\Webapi\Api\Admin\Role\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
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
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE_DELETE, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $oldRole = $this->roleRepository->findRoleByRoleId($validator->getRoleId());

        if ($oldRole->isEditable() === false) {
            return Result::createError('This role cannot be edited', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $updatedRole = $this->roleRepository->updateRoleByRoleId(
            $validator->getRoleId(),
            $validator->getRoleName(),
            $validator->getRoleDescription(),
            $validator->getRoleColor(),
            $validator->getAccessLevel(),
            $validator->appliesToEveryone(),
            $validator->isDisabled()
        );

        if ($updatedRole === null) {
            return Result::createError('Role does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $this->responder->render($updatedRole);
    }
}
