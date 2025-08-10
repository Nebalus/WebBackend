<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Delete;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class DeleteRolePermissionResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("ROLE PERMISSION DELETE");
    }
}
