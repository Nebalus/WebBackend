<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeUsername;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class ChangeUsernameAction extends AbstractAction
{
    public function __construct(
        private readonly ChangeUsernameValidator $validator,
        private readonly ChangeUsernameService $service,
    ) {
    }

    /**
     * @throws ApiException
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
