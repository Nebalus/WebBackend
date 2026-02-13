<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\Auth;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Exception\ApiException;
use ReallySimpleJWT\Exception\BuildException;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class AuthUserAction extends AbstractAction
{
    public function __construct(
        private readonly AuthUserValidator $validator,
        private readonly AuthUserService $service,
    ) {
    }

    /**
     * @throws ApiException | BuildException
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $clientIp = $request->getAttribute(AttributeTypes::CLIENT_IP);
        $result = $this->service->execute($this->validator, $clientIp);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
