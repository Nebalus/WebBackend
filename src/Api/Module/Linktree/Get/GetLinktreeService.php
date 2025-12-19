<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Get;

use Nebalus\Webapi\Slim\ResultInterface;

readonly class GetLinktreeService
{
    public function __construct(
        private GetLinktreeResponder $view,
    ) {
    }

    public function execute(GetLinktreeValidator $validator): ResultInterface
    {
        return $this->view->render();
    }
}
