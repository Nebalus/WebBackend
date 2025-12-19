<?php

namespace Nebalus\Webapi\Api;

use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

abstract class AbstractAction
{
    /**
     * @throws ApiException
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): ResponseInterface {
        if (isset($args['user_id']) && $args['user_id'] === "self" && $request->getAttribute(AttributeTypes::REQUESTING_USER, null) !== null) {
            $requestingUser = $request->getAttribute(AttributeTypes::REQUESTING_USER);
            $args['user_id'] = $requestingUser->getUserId()->asInt();
        }

        return $this->execute($request, $response, $args);
    }

    /**
     * @throws ApiException
     */
    abstract protected function execute(
        Request $request,
        Response $response,
        array $pathArgs
    ): ResponseInterface;
}
