<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Referral\GetAll;

use Nebalus\Webapi\Api\AbstractAction;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class GetAllReferralAction extends AbstractAction
{
    public function __construct(
        private readonly GetAllReferralService $service
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $result = $this->service->execute($request->getAttribute('user'));

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
