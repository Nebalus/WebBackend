<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Verify;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Exception\ApiException;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class VerifyChangeEmailAction extends AbstractAction
{
    public function __construct(
        private readonly VerifyChangeEmailValidator $validator,
        private readonly VerifyChangeEmailService $service,
    ) {
    }

    /**
     * @throws ApiException
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $result = $this->service->execute($this->validator);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
