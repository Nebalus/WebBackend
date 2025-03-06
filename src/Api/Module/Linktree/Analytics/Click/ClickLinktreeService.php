<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Analytics\Click;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class ClickLinktreeService
{
    public function __construct()
    {
    }

    public function execute(ClickLinktreeValidator $validator): ResultInterface
    {
        return ClickLinktreeView::render();
    }
}
