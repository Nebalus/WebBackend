<?php

namespace Nebalus\Webapi\Api\Admin\Permission\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\AbstractService;
use Nebalus\Webapi\Config\Types\PermissionNodesTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\PermissionsRepository\MySqlPermissionRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

class GetPermissionService extends AbstractService
{
    public function __construct(
        private readonly MySqlPermissionRepository $permissionRepository,
        private readonly GetPermissionResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function execute(GetPermissionValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::ADMIN_ROLE, true))) {
            $requestedPermission = $this->permissionRepository->findPermissionByPermissionId($validator->getPermissionId());

            if ($requestedPermission === null) {
                return Result::createError("Permission not found", StatusCodeInterface::STATUS_NOT_FOUND);
            }

            return $this->responder->render($requestedPermission);
        }

        return ResultBuilder::buildNoPermissionResult();
    }
}
