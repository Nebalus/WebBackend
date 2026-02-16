<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ResetPassword\Verify;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class VerifyResetPasswordResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess('Password has been reset successfully', StatusCodeInterface::STATUS_OK);
    }
}
