<?php

namespace Nebalus\Webapi\Api\Action\Referral\Analytics;

use DateMalformedStringException;
use Nebalus\Webapi\Api\Action\ApiAction;
use Nebalus\Webapi\Api\Service\Referral\Analytics\ReferralClickService;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class ReferralClickHistoryAction extends ApiAction
{
    public function __construct(
        private readonly ReferralClick $referralClickService,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    protected function execute(Request $request, Response $response, array $args): Response
    {
        $params = $request->getParams() ?? [];
        $result = $this->referralClickService->execute($params);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
