<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Create;

use Nebalus\Webapi\Value\Result\ResultInterface;

readonly class CreateLinktreeService
{
    public function __construct()
    {
    }

    public function execute(array $params): ResultInterface
    {
        return CreateLinktreeView::render();
    }
}
