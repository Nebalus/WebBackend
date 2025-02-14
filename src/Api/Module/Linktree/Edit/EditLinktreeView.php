<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

class EditLinktreeView
{
    public static function render(): ResultInterface
    {
        $fields = [];

        return Result::createSuccess("PLACEHOLDER", 200, $fields);
    }
}
