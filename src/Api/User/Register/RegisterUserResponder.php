<?php

namespace Nebalus\Webapi\Api\User\Register;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class RegisterUserResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("User registered", StatusCodeInterface::STATUS_CREATED, []);
    }
}
