<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Get;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class GetLinktreeService
{
    public function __construct(
    ) {
    }

    public function execute(array $params): ResultInterface
    {
        return GetLinktreeView::render();
    }
}
