<?php

namespace Nebalus\Webapi\Api\Health;

use Nebalus\Webapi\Api\AbstractAction;
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
        $response->getBody()->write("This service is healthy");
        return $response->withHeader('Content-Type', "text/html");
    }
}
