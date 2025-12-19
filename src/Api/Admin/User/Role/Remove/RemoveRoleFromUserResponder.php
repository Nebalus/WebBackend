<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\Remove;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;

class RemoveRoleFromUserResponder
{
    public function render(Role $role): ResultInterface
    {
        $fields = [
            'role_id' => $role->getRoleId()->asInt(),
            'name' => $role->getName()->asString(),
            'description' => $role->getDescription()?->asString(),
            'color' => $role->getColor()->asString(),
            'access_level' => $role->getAccessLevel()->asInt(),
            'applies_to_everyone' => $role->appliesToEveryone(),
        ];

        return Result::createSuccess("Role Removed", StatusCodeInterface::STATUS_OK, $fields);
    }
}
