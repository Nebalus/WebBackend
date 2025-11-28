<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\GetAll;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;

class GetAllRoleFromUserValidator extends AbstractValidator
{
    private RoleId $roleId;
    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "role_id" => RoleId::getSchema(),
            ])
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->roleId = RoleId::from($pathArgsData["role_id"]);
    }

    public function getRoleId(): RoleId
    {
        return $this->roleId;
    }
}
