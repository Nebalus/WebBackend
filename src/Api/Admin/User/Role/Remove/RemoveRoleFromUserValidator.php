<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Delete;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;

class RemoveRoleFromUserValidator extends AbstractValidator
{
    private RoleId $roleId;
    private PermissionNodeCollection $permissionNodes;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "role_id" => RoleId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::array(PermissionNode::getSchema())
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->roleId = RoleId::from($pathArgsData["role_id"]);
        $this->permissionNodes = PermissionNodeCollection::fromObjects(
            ...array_map(
                fn(string $node) => PermissionNode::from($node),
                $bodyData
            )
        );
    }

    public function getRoleId(): RoleId
    {
        return $this->roleId;
    }

    public function getPermissionNodes(): PermissionNodeCollection
    {
        return $this->permissionNodes;
    }
}
