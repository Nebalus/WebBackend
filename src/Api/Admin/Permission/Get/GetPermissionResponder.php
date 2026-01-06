<?php

namespace Nebalus\Webapi\Api\Admin\Permission\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\Permission;

class GetPermissionResponder
{
    public function render(Permission $permission): ResultInterface
    {
        $fields = [
            "permission_id" => $permission->getPermissionId()->asInt(),
            "node" => $permission->getNode()->asString(),
            "description" => $permission->getDescription()?->asString(),
            "default_value" => $permission->getDefaultValue()?->asInt(),
            "prestige_level" => [
                "type" => $permission->getPrestigeLevel()->asString(),
                "value" => $permission->getPrestigeLevel()->asInt(),
            ]
        ];

        return Result::createSuccess("Permission fetched", StatusCodeInterface::STATUS_OK, $fields);
    }
}
