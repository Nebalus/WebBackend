<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Analytics\GetPublicDetail;

use Nebalus\Webapi\Api\AbstractAction;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetDetailPublicBlogAction extends AbstractAction
{
    public function __construct(
        private readonly GetDetailPublicBlogService $service
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $slug = $pathArgs['slug'];
        $result = $this->service->execute($slug);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
