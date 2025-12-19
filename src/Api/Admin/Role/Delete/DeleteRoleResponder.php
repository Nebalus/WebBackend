<?php

namespace Nebalus\Webapi\Api\Admin\Role\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class DeleteRoleResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("Role Deleted", StatusCodeInterface::STATUS_OK);
    }
}
