<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;

class DeleteRolePermissionResponder
{
    public function render(PermissionRoleLinkCollection $permissionsLinksDiff): ResultInterface
    {
        $fields = [
            "deleted_permissions" => []
        ];
        foreach ($permissionsLinksDiff as $permissionRoleLink) {
            $fields["deleted_permissions"][] = [
                'node' => $permissionRoleLink->getNode()->asString(),
                'allow_all_sub_permissions' => $permissionRoleLink->getMetadata()->allowAllSubPermissions(),
                'value' => $permissionRoleLink->getMetadata()->getValue()?->asInt(),
            ];
        }

        return Result::createSuccess("All requested permissions deleted successfully", StatusCodeInterface::STATUS_OK, $fields);
    }
}
