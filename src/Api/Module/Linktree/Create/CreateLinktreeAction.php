<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Linktree\Create;

use Nebalus\Webapi\Api\AbstractAction;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class CreateLinktreeAction extends AbstractAction
{
    public function __construct(
        private readonly CreateLinktreeService $service,
        private readonly CreateLinktreeValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $result = $this->service->execute($this->validator);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
