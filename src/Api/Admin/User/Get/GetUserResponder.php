<?php

namespace Nebalus\Webapi\Api\Admin\User\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserAccount;

class GetUserResponder
{
    public function render(UserAccount $user): ResultInterface
    {
        $fields = [
            'user_id' => $user->getUserId()?->asInt(),
            'username' => $user->getUsername()->asString(),
            'profile_image_id' => $user->getProfileImageId(),
            'email' => $user->getEmail()->asString(),
            'email_verified' => $user->isEmailVerified(),
            'disabled' => $user->isDisabled(),
            'disabled_by' => $user->getDisabledBy()?->asInt(),
            'disabled_reason' => $user->getDisabledReason(),
            'disabled_at' => $user->getDisabledAt()?->format(DATE_ATOM),
            'created_at' => $user->getCreatedAtDate()->format(DATE_ATOM),
            'password_updated_at' => $user->getPasswordUpdatedAtDate()->format(DATE_ATOM),
        ];

        return Result::createSuccess("User Fetched", StatusCodeInterface::STATUS_OK, $fields);
    }
}
