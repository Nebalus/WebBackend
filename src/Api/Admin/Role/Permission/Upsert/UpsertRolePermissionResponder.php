<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Upsert;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;

class UpsertRolePermissionResponder
{
    public function render(PermissionRoleLinkCollection $permissionsLinksDiff): ResultInterface
    {
        $fields = [
            "upserted_permissions" => []
        ];
        foreach ($permissionsLinksDiff as $permissionRoleLink) {
            $fields["upserted_permissions"] = [
                'node' => $permissionRoleLink->getNode()->asString(),
                'allow_all_sub_permissions' => $permissionRoleLink->getMetadata()->allowAllSubPermissions(),
                'value' => $permissionRoleLink->getMetadata()->getValue()?->asInt(),
            ];
        }

        return Result::createSuccess("Permissions upserted successfully", StatusCodeInterface::STATUS_CREATED, $fields);
    }
}
