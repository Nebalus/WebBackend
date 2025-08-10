<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\GetAll;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class GetAllRolePermissionResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("ROLE PERMISSION GETALL");
    }
}
