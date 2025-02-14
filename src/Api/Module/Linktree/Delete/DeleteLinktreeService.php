<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Delete;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class DeleteLinktreeService
{
    public function __construct(
    ) {
    }

    public function execute(array $params): ResultInterface
    {
        return DeleteLinktreeView::render();
    }
}
