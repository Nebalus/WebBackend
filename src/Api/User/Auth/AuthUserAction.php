<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\Auth;

use Nebalus\Webapi\Api\AbstractAction;
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

        $result = $this->service->execute($this->validator);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
