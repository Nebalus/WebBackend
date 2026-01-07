<?php

namespace Nebalus\Webapi\Api\User\GetUserPermissions;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserId;

class GetUserPermissionsResponder
{
    public function render(UserId $userId, UserPermissionIndex $userPermissionIndex): ResultInterface
    {
        $fields = [
            "user_id" => $userId->asInt(),
        ];

        $permissions = array_map(function ($permission) {
            return $permission->asArray();
        }, $userPermissionIndex->asArray());

        $fields["permissions"] = $permissions;

        return Result::createSuccess("List of all permissions for the requested user", StatusCodeInterface::STATUS_OK, $fields);
    }
}
