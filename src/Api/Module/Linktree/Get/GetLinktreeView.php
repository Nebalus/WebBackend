<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Get;

use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

class GetLinktreeView
{
    public static function render(): ResultInterface
    {
        $fields = [];

        return Result::createSuccess("PLACEHOLDER", 200, $fields);
    }
}
