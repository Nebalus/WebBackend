<?php

namespace Nebalus\Webapi\Api\Admin\User\GetAll;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetAllUserAction extends AbstractAction
{
    public function __construct(
        private readonly GetAllUserService $service
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): ResponseInterface
    {
        $userPerms = $request->getAttribute(AttributeTypes::CLIENT_USER_PERMISSION_INDEX);
        $result = $this->service->execute($userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
