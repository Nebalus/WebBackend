<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Public\GetAll;

use Nebalus\Webapi\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

readonly class GetAllPublicBlogAction
{
    public function __construct(
        private GetAllPublicBlogService $service,
        private GetAllPublicBlogValidator $validator
    ) {
    }

    /**
     * @throws ApiException
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        $this->validator->validate($request, $args);
        $result = $this->service->execute($this->validator);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
