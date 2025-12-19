<?php

namespace Nebalus\Webapi\Api\Admin\User\GetAll;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserAccount;

class GetAllUserResponder
{
    /**
     * @param UserAccount[] $users
     */
    public function render(array $users): ResultInterface
    {
        $payload = array_map(fn($user) => $user->asArray(), $users);
        return Result::createSuccess("List of all users", 200, $payload);
    }
}
