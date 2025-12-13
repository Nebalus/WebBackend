<?php

namespace Nebalus\Webapi\Api\Admin\User\GetAll;

use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetAllUserService
{
    public function __construct(
        private GetAllUserResponder $responder
    ) {
    }

    public function execute(GetAllUserValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        return $this->responder->render();
    }
}
