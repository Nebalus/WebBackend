<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\Remove;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class RemoveRoleFromUserResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("Role Removed", StatusCodeInterface::STATUS_OK);
    }
}
