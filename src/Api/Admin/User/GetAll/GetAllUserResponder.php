<?php

namespace Nebalus\Webapi\Api\Admin\User\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkMetadata;

class GetAllUserResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("GET ALL USER ENDPOINT");
    }
}
