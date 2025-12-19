<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Delete;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class DeleteRolePermissionAction extends AbstractAction
{
    public function __construct(
        private readonly DeleteRolePermissionService $service,
        private readonly DeleteRolePermissionValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): ResponseInterface
    {
        $this->validator->validate($request, $pathArgs);

        $userPerms = $request->getAttribute(AttributeTypes::USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
