<?php

namespace Nebalus\Webapi\Api\Info;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Value\Result\Result;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class InfoAction extends AbstractAction
{
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $result = Result::createError("This is the API for my website https://www.nebalus.dev", StatusCodeInterface::STATUS_NOT_FOUND);
        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
