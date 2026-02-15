<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Published\GetDetail;

use Nebalus\Webapi\Api\AbstractAction;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class GetDetailPublishedBlogAction extends AbstractAction
{
    public function __construct(
        private readonly GetDetailPublishedBlogService $service
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $slug = $pathArgs['slug'];
        $result = $this->service->execute($slug);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
