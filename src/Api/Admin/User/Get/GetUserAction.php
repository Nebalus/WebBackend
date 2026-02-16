<?php

namespace Nebalus\Webapi\Api\Admin\User\Get;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetUserAction extends AbstractAction
{
    public function __construct(
        private readonly GetUserService $service,
        private readonly GetUserValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $userPerms = $request->getAttribute(AttributeTypes::CLIENT_USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
