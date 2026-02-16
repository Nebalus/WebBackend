<?php

namespace Nebalus\Webapi\Api\Admin\User\Disable;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\UserAccount;

class DisableUserResponder
{
    public function render(UserAccount $user): ResultInterface
    {
        $fields = [
            'user_id' => $user->getUserId()?->asInt(),
            'username' => $user->getUsername()->asString(),
            'disabled' => $user->isDisabled(),
            'disabled_by' => $user->getDisabledBy()?->asInt(),
            'disabled_reason' => $user->getDisabledReason(),
            'disabled_at' => $user->getDisabledAt()?->format(DATE_ATOM),
        ];

        return Result::createSuccess(
            $user->isDisabled() ? "User Disabled" : "User Enabled",
            StatusCodeInterface::STATUS_OK,
            $fields
        );
    }
}
