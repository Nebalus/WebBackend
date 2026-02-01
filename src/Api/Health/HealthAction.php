<?php

namespace Nebalus\Webapi\Api\Health;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Value\Result\Result;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use Throwable;

class HealthAction extends AbstractAction
{
    public function __construct()
    {
    }

    /**
     * @throws Throwable
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $result = Result::createSuccess("This service is healthy", StatusCodeInterface::STATUS_OK, [
            "status" => "HEALTHY",
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
