<?php

namespace Nebalus\Webapi\Api\User\Auth;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Internal\Result;
use Nebalus\Webapi\Value\User\User;
use ReallySimpleJWT\Jwt;

class AuthUserView
{
    public function render(Jwt $jwt, User $user): ResultInterface
    {
        $fields = [
            "jwt" => $jwt->getToken(),
            "user" => [
                "username" => $user->getUsername()->asString(),
                "email" => $user->getEmail()->asString(),
                "disabled" => $user->isDisabled(),
                "created_at" => $user->getCreatedAtDate()->format(DATE_ATOM),
                "updated_at" => $user->getUpdatedAtDate()->format(DATE_ATOM),
            ]
        ];

        return Result::createSuccess("User authenticated", StatusCodeInterface::STATUS_OK, $fields);
    }
}
