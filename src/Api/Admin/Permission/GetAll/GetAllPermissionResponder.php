<?php

namespace Nebalus\Webapi\Api\Admin\Permission\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionCollection;

class GetAllPermissionResponder
{
    public function render(PermissionCollection $permissionCollection): ResultInterface
    {
        $fields = [];
        foreach ($permissionCollection as $permission) {
            $fields[] = [
                "permission_id" => $permission->getPermissionId()->asInt(),
                "node" => $permission->getNode()->asString(),
                "description" => $permission->getDescription()->asString(),
                "prestige_level" => $permission->getPrestigeLevel()->asString(),
                "default_value" => $permission->getDefaultValue()?->asInt(),
            ];
        }

        return Result::createSuccess("List of permissions found", StatusCodeInterface::STATUS_OK, $fields);
    }
}
