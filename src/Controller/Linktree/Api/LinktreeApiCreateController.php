<?php

namespace Nebalus\Ownsite\Controller\Linktree\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LinktreeApiCreateController
{

    public function action(Request $request, Response $response): Response
    {
        $response->getBody()->write("Linktree Api Create");

        return $response->withStatus(200);
    }

}