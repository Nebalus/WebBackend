<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Slim\ResultInterface;

readonly class EditLinktreeService
{
    public function __construct(
        private EditLinktreeResponder $view,
    ) {
    }

    public function execute(EditLinktreeValidator $validator): ResultInterface
    {
        return $this->view->render();
    }
}
