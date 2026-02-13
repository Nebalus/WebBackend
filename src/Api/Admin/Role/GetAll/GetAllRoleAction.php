<?php

namespace Nebalus\Webapi\Api\Admin\Role\GetAll;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetAllRoleAction extends AbstractAction
{
    public function __construct(
        private readonly GetAllRoleService $service
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $userPerms = $request->getAttribute(AttributeTypes::CLIENT_USER_PERMISSION_INDEX);
        $result = $this->service->execute($userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
