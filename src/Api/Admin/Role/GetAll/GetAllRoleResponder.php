<?php

namespace Nebalus\Webapi\Api\Admin\Role\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;

class GetAllRoleResponder
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
                'deletable' => $role->isDeletable(),
                'editable' => $role->isEditable(),
                'disabled' => $role->isDisabled(),
                "created_at" => $role->getCreatedAtDate()->format(DATE_ATOM),
                "updated_at" => $role->getUpdatedAtDate()->format(DATE_ATOM),
            ];
        }
        return Result::createSuccess("List of all roles", StatusCodeInterface::STATUS_OK, $fields);
    }
}
