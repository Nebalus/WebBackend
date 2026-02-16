<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Verify;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserEmail;

class VerifyChangeEmailResponder
{
    public function render(UserEmail $email): ResultInterface
    {
        $fields = [
            'email' => $email->asString(),
        ];

        return Result::createSuccess('Email updated successfully', StatusCodeInterface::STATUS_OK, $fields);
    }
}
