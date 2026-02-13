<?php

namespace Nebalus\Webapi\Api\User\GetUserPermissions;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetUserPermissionsAction extends AbstractAction
{
    public function __construct(
        private readonly GetUserPermissionsService $service,
        private readonly GetUserPermissionsValidator $validator
    ) {
    }

    /**
     * @inheritDoc
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $requestingUser = $request->getAttribute(AttributeTypes::CLIENT_USER);
        $userPerms = $request->getAttribute(AttributeTypes::CLIENT_USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
