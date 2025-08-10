<?php

namespace Nebalus\Webapi\Api\Admin\Role\Permission\Upsert;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkMetadata;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;

class UpsertRolePermissionValidator extends AbstractValidator
{
    private RoleId $roleId;
    private PermissionRoleLinkCollection $permissionRoleLinks;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "role_id" => RoleId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::array(S::object([
                "node" => PermissionNode::getSchema(),
                "allow_all_sub_permissions" => S::boolean()->default(false),
                "value" => PermissionValue::getSchema()->nullish()->default(null),
            ]))
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->roleId = RoleId::from($pathArgsData["role_id"]);
        $this->permissionRoleLinks = PermissionRoleLinkCollection::fromObjects(
            ...array_map(
                fn(array $data) => PermissionRoleLink::fromMetadata(
                    PermissionNode::from($data['node']),
                    PermissionRoleLinkMetadata::fromArray($data)
                ),
                $bodyData
            )
        );
    }

    public function getRoleId(): RoleId
    {
        return $this->roleId;
    }

    public function getPermissionRoleLinks(): PermissionRoleLinkCollection
    {
        return $this->permissionRoleLinks;
    }
}
