<?php

namespace Nebalus\Webapi\Api\User\Auth;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserAccount;
use ReallySimpleJWT\Jwt;

class AuthUserResponder
{
    public function render(Jwt $jwt, UserAccount $user): ResultInterface
    {
        $fields = [
            "jwt" => $jwt->getToken(),
            "user" => [
                "username" => $user->getUsername()->asString(),
                "email" => $user->getEmail()->asString(),
                "disabled" => $user->isDisabled(),
                "created_at" => $user->getCreatedAtDate()->format(DATE_ATOM),
                "password_updated_at" => $user->getPasswordUpdatedAtDate()->format(DATE_ATOM)
            ]
        ];

        return Result::createSuccess("User authenticated", StatusCodeInterface::STATUS_OK, $fields);
    }
}
