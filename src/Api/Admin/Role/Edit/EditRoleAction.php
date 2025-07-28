<?php

namespace Nebalus\Webapi\Api\Admin\Role\Edit;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class EditRoleAction extends AbstractAction
{
    public function __construct(
        private readonly EditRoleService $service,
        private readonly EditRoleValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $userPerms = $request->getAttribute(AttributeTypes::USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
