<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;

class GetAllRoleFromUserResponder
{
    public function render(RoleCollection $roleCollection): ResultInterface
    {
        $fields = [];
        foreach ($roleCollection as $role) {
            $fields[] = [
                'role_id' => $role->getRoleId()->asInt(),
                'name' => $role->getName()->asString(),
                'description' => $role->getDescription()?->asString(),
                'color' => $role->getColor()->asString(),
                'access_level' => $role->getAccessLevel()->asInt(),
                'applies_to_everyone' => $role->appliesToEveryone(),
            ];
        }

        return Result::createSuccess("Fetched all roles from this user", StatusCodeInterface::STATUS_OK, $fields);
    }
}
