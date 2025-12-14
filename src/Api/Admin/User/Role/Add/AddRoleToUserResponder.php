<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\Add;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class AddRoleToUserResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("Role Added", StatusCodeInterface::STATUS_CREATED);
    }
}
