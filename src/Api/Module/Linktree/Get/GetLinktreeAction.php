<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Linktree\Get;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccessCollection;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class GetLinktreeAction extends AbstractAction
{
    public function __construct(
        private readonly GetLinktreeService $service,
        private readonly GetLinktreeValidator $validator
    ) {
    }

    protected function endpointAccessGuard(): PermissionAccessCollection
    {
        return PermissionAccessCollection::fromObjects();
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);
        $result = $this->service->execute($this->validator);
        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
