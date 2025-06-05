<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class CreateLinktreeView
{
    public function render(): ResultInterface
    {
        $fields = [];

        return Result::createSuccess("PLACEHOLDER", StatusCodeInterface::STATUS_OK, $fields);
    }
}
