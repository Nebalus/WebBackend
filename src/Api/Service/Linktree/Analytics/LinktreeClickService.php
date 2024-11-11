<?php

namespace Nebalus\Webapi\Api\Service\Linktree\Analytics;

use Nebalus\Webapi\Api\View\Linktree\Analytics\LinktreeClickView;
use Nebalus\Webapi\Value\Result\ResultInterface;

readonly class LinktreeClickService
{
    public function __construct()
    {
    }

    public function execute(array $params): ResultInterface
    {
        return LinktreeClickView::render();
    }
}
