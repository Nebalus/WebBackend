<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Action\Referral;

use Nebalus\Webapi\Api\Action\ApiAction;
use Nebalus\Webapi\Api\Service\Referral\ReferralDeleteService;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class ReferralDeleteAction extends ApiAction
{
    public function __construct(
        private readonly ReferralDeleteService $referralDeleteService
    ) {
    }

    protected function execute(Request $request, Response $response, array $args): Response
    {
        $params = $request->getParams() ?? [];
        $result = $this->referralDeleteService->execute($params);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
