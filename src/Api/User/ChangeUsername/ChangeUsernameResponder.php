<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeUsername;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\Username;

class ChangeUsernameResponder
{
    public function render(Username $username): ResultInterface
    {
        $fields = [
            'username' => $username->asString(),
        ];

        return Result::createSuccess('Username updated successfully', StatusCodeInterface::STATUS_OK, $fields);
    }
}
