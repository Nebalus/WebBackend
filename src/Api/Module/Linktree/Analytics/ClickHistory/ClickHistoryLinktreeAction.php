<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Linktree\Analytics\ClickHistory;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Api\Module\Linktree\Analytics\Click\ClickLinktreeService;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class ClickHistoryLinktreeAction extends AbstractAction
{
    public function __construct(
        private readonly ClickLinktreeService $service,
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $params = $request->getParams() ?? [];
        $result = $this->service->execute($params);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
