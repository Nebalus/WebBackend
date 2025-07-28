<?php

namespace Nebalus\Webapi\Api\Admin\Role\EditPermission;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleValidator;
use Nebalus\Webapi\Api\Admin\Role\Get\GetRoleResponder;
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

readonly class EditPermissionRoleService
{
    public function __construct(
        private MySqlRoleRepository $roleRepository,
        private EditPermissionRoleResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function execute(EditPermissionRoleValidator $validator, string $httpMethod, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::ADMIN_ROLE_EDIT, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $role = $this->roleRepository->findRoleById($validator->getRoleId());

        if ($role === null) {
            return Result::createError('Role does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($role->isEditable() === false) {
            return Result::createError('This role cannot be edited', StatusCodeInterface::STATUS_FORBIDDEN);
        }

        return match ($httpMethod) {
            RequestMethodInterface::METHOD_GET => $this->responder->renderGet(),
            RequestMethodInterface::METHOD_PUT => $this->responder->renderPut(),
            RequestMethodInterface::METHOD_DELETE => $this->responder->renderDelete(),
            default => Result::createError('Invalid HTTP method', StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED),
        };
    }
}
