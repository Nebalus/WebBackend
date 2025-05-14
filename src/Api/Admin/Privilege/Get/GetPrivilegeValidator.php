<?php

namespace Nebalus\Webapi\Api\Admin\Privilege\Get;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Api\RequestParamTypes;
use Nebalus\Webapi\Value\User\AccessControl\Privilege\PrivilegeNode;

class GetPrivilegeValidator extends AbstractValidator
{
    protected function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "privilegeNode" => PrivilegeNode::getSchema(),
            ]),
        ]));
    }

    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        // TODO: Implement onValidate() method.
    }
}
