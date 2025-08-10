<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Upsert;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class UpsertRolePermissionResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("ROLE PERMISSION PUT");
    }
}
