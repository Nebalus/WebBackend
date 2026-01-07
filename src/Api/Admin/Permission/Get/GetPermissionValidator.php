<?php

namespace Nebalus\Webapi\Api\Admin\Permission\Get;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionId;

class GetPermissionValidator extends AbstractValidator
{
    private PermissionId $privilegeId;
    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "permission_id" => PermissionId::getSchema(),
            ]),
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->privilegeId = PermissionId::from($pathArgsData["permission_id"]);
    }

    public function getPermissionId(): PermissionId
    {
        return $this->privilegeId;
    }
}
