<?php

namespace Nebalus\Webapi\Api\User\GetPrivileges;

use Nebalus\Webapi\Api\AbstractAction;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetPrivilegesUserAction extends AbstractAction
{

    /**
     * @inheritDoc
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $result = $this->service->execute($this->validator);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
