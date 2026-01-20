<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\GetPublic;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class GetPublicBlogAction
{
    public function __construct(
        private readonly GetPublicBlogService $service,
        private readonly GetPublicBlogValidator $validator
    ) {
    }

    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        $this->validator->validate($request, $args);
        $result = $this->service->execute($this->validator);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
