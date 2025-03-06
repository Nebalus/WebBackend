<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class EditLinktreeService
{
    public function __construct(
    ) {
    }

    public function execute(EditLinktreeValidator $validator): ResultInterface
    {
        return EditLinktreeView::render();
    }
}
