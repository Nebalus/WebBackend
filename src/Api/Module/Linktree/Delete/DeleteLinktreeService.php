<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Delete;

use Nebalus\Webapi\Slim\ResultInterface;

readonly class DeleteLinktreeService
{
    public function __construct(
        private DeleteLinktreeResponder $view,
    ) {
    }

    public function execute(DeleteLinktreeValidator $validator): ResultInterface
    {
        return $this->view->render();
    }
}
