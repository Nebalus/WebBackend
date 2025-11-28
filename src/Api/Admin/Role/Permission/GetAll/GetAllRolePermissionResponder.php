<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;

class GetAllRolePermissionResponder
{
    public function render(PermissionRoleLinkCollection $permissionRoleLinkCollection): ResultInterface
    {
        $fields = [];
        foreach ($permissionRoleLinkCollection as $permissionRoleLink) {
            $fields[] = [
                'node' => $permissionRoleLink->getNode()->asString(),
                'allow_all_sub_permissions' => $permissionRoleLink->getMetadata()->allowAllSubPermissions(),
                'value' => $permissionRoleLink->getMetadata()->getValue()?->asInt(),
            ];
        }

        return Result::createSuccess("Fetched permissions for this role", StatusCodeInterface::STATUS_OK, $fields);
    }
}
