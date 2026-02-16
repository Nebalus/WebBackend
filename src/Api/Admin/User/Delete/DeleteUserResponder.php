<?php

namespace Nebalus\Webapi\Api\Admin\User\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class DeleteUserResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("User Deleted", StatusCodeInterface::STATUS_OK);
    }
}
