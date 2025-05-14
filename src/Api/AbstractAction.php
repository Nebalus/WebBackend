<?php

namespace Nebalus\Webapi\Api;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Privilege\PrivilegeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Privilege\PrivilegeNodeCollection;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

abstract class AbstractAction
{
    public function __construct(
        private PrivilegeNodeCollection $neededEndpointPrivileges
    ) {
    }

    /**
     * @throws ApiException
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): ResponseInterface {
        $authType = $request->getAttribute('authType');
        if ($authType === 'jwt') {
            $userPrivileges = $request->getAttribute('userPrivileges');
            if ($userPrivileges instanceof PrivilegeNodeCollection) {
                $endpointPrivileges = $this->neededEndpointPrivileges;
                $userPrivileges->containsSomeNodes($endpointPrivileges);
            }

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
    ): Response;
}
