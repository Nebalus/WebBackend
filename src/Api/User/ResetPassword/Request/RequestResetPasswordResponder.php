<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ResetPassword\Request;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class RequestResetPasswordResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess('If a matching account exists, a password reset email has been sent.', StatusCodeInterface::STATUS_OK);
    }
}
