<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Request;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class RequestChangeEmailResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess('Verification email sent. Please check your inbox.', StatusCodeInterface::STATUS_OK);
    }
}
