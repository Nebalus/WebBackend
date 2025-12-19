<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Click;


use Nebalus\Webapi\Slim\ResultInterface;

readonly class ClickLinktreeService
{
    public function __construct(
        private ClickLinktreeResponder $view,
    ) {
    }

    public function execute(ClickLinktreeValidator $validator): ResultInterface
    {
        return $this->view->render();
    }
}
