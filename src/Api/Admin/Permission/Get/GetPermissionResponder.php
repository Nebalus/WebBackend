<?php

namespace Nebalus\Webapi\Api\Admin\Permission\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\Permission;

class GetPermissionResponder
{
    public function render(Permission $privilege): ResultInterface
    {
        $fields = [
            "permission_id" => $privilege->getPermissionId()->asInt(),
            "node" => $privilege->getNode()->asString(),
            "description" => $privilege->getDescription()->asString(),
            "prestige_level" => $privilege->getPrestigeLevel()->asString(),
            "default_value" => $privilege->getDefaultValue()?->asInt(),
        ];

        return Result::createSuccess("Permission fetched", StatusCodeInterface::STATUS_OK, $fields);
    }
}
