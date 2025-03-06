<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Create;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class CreateLinktreeService
{
    public function __construct()
    {
    }

    public function execute(CreateLinktreeValidator $validator): ResultInterface
    {
        return CreateLinktreeView::render();
    }
}
