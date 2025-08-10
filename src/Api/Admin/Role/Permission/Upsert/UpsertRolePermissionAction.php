<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Upsert;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class UpsertRolePermissionAction extends AbstractAction
{
    public function __construct(
        private readonly UpsertRolePermissionService $service,
        private readonly UpsertRolePermissionValidator $validator
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
