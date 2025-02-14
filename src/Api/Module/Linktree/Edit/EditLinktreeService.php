<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class EditLinktreeService
{
    public function __construct(
    ) {
    }

    public function execute(array $params): ResultInterface
    {
        return EditLinktreeView::render();
    }
}
