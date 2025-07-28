<?php

namespace Nebalus\Webapi\Api\Admin\Role\EditPermission;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleService;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class EditPermissionRoleAction extends AbstractAction
{
    public function __construct(
        private readonly EditPermissionRoleService $service,
        private readonly EditPermissionRoleValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): ResponseInterface
    {
        $this->validator->validate($request, $pathArgs);

        $httpMethod = $request->getMethod();
        $userPerms = $request->getAttribute(AttributeTypes::USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $httpMethod, $userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
