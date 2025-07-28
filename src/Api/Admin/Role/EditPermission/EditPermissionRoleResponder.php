<?php

namespace Nebalus\Webapi\Api\Admin\Role\EditPermission;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class EditPermissionRoleResponder
{
    public function renderGet(): ResultInterface
    {
        return Result::createSuccess("ROLE PERMISSION GET");
    }

    public function renderPut(): ResultInterface
    {
        return Result::createSuccess("ROLE PERMISSION PUT");
    }

    public function renderDelete(): ResultInterface
    {
        return Result::createSuccess("ROLE PERMISSION DELETE");
    }
}