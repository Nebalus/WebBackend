<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Referral\Create;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Value\User\AccessControl\Privilege\PrivilegeNodeCollection;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class CreateReferralAction extends AbstractAction
{
    public function __construct(
        private readonly CreateReferralService $service,
        private readonly CreateReferralValidator $validator
    ) {
    }

    protected function privilegeConfig(): PrivilegeNodeCollection
    {
        return PrivilegeNodeCollection::fromObjects();
    }
    
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $result = $this->service->execute($this->validator, $request->getAttribute('user'));

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
