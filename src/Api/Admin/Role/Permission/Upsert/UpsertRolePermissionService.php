<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Upsert;

use Fig\Http\Message\RequestMethodInterface;
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

readonly class UpsertRolePermissionService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private UpsertRolePermissionResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function execute(UpsertRolePermissionValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE_EDIT, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $role = $this->roleRepository->findRoleByRoleId($validator->getRoleId());

        if ($role === null) {
            return Result::createError('Role does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($role->isEditable() === false) {
            return Result::createError('This role cannot be edited', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $this->roleRepository->upsertPermissionLinksToRoleByRoleId(
            $validator->getRoleId(),
            $validator->getPermissionRoleLinks()
        );

        return $this->responder->render();
    }
}
