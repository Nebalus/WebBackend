<?php

namespace Nebalus\Webapi\Api\Admin\User\Disable;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class DisableUserAction extends AbstractAction
{
    public function __construct(
        private readonly DisableUserService $service,
        private readonly DisableUserValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $userPerms = $request->getAttribute(AttributeTypes::CLIENT_USER_PERMISSION_INDEX);
        $clientUser = $request->getAttribute(AttributeTypes::CLIENT_USER);
        $result = $this->service->execute($this->validator, $userPerms, $clientUser);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
