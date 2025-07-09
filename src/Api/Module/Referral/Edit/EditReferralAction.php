<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class EditReferralAction extends AbstractAction
{
    public function __construct(
        private readonly EditReferralService $service,
        private readonly EditReferralValidator $validator,
    ) {
    }

    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $requestingUser = $request->getAttribute(AttributeTypes::REQUESTING_USER);
        $userPermissionIndex = $request->getAttribute(AttributeTypes::USER_PERMISSION_INDEX);
        $result = $this->service->execute($this->validator, $requestingUser, $userPermissionIndex);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
