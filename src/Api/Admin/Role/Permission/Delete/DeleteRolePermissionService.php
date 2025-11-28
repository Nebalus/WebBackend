<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Delete;

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
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class DeleteRolePermissionService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private RemoveRoleFromUserResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function execute(RemoveRoleFromUserValidator $validator, UserPermissionIndex $userPerms): ResultInterface
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

        $permissionsLinksBefore = $this->roleRepository->getAllPermissionLinksByRoleId($validator->getRoleId());

        $this->roleRepository->deletePermissionLinksByRoleId(
            $validator->getRoleId(),
            $validator->getPermissionNodes()
        );

        $permissionsLinksAfter = $this->roleRepository->getAllPermissionLinksByRoleId($validator->getRoleId());

        // Calculate the difference between before and after and return only the deleted links
        $permissionsLinksDiff = PermissionRoleLinkCollection::fromObjects(...array_filter(
            [...$permissionsLinksBefore],
            function ($beforeLink) use ($permissionsLinksAfter) {
                foreach ($permissionsLinksAfter as $afterLink) {
                    if ($afterLink == $beforeLink) {
                        return false;
                    }
                }
                return true;
            }
        ));

        if ($permissionsLinksDiff->isEmpty()) {
            return Result::createError('No changes were made to the role permissions', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $this->responder->render($permissionsLinksDiff);
    }
}
