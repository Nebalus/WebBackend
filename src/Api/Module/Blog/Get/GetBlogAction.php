<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Get;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class GetBlogAction extends AbstractAction
{
    public function __construct(
        private readonly GetBlogService $service,
        private readonly GetBlogValidator $validator
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $requestingUser = $request->getAttribute(AttributeTypes::REQUESTING_USER);
        $userPerms = $request->getAttribute(AttributeTypes::USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}