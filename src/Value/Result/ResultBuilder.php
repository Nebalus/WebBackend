<?php

namespace Nebalus\Webapi\Value\Result;

use Fig\Http\Message\StatusCodeInterface;

readonly class ResultBuilder
{
    public static function buildNoPermissionResult(): Result
    {
        return Result::createError("You do not have enough permissions to access this resource", StatusCodeInterface::STATUS_FORBIDDEN);
    }
}
