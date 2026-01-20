<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Delete;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class DeleteBlogAction extends AbstractAction
{
    public function __construct(
        private readonly DeleteBlogService $service,
        private readonly DeleteBlogValidator $validator
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