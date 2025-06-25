<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\Click;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccessCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class ClickReferralAction extends AbstractAction
{
    public function __construct(
        private readonly ClickReferralValidator $validator,
        private readonly ClickReferralService $service,
    ) {
    }

    protected function endpointAccessGuard(): PermissionAccessCollection
    {
        return PermissionAccessCollection::fromObjects();
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);
        $result = $this->service->execute($this->validator);
        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
