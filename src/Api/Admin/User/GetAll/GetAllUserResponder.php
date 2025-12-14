<?php

namespace Nebalus\Webapi\Api\Admin\User\GetAll;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class GetAllUserResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("GET ALL USER ENDPOINT");
    }
}
