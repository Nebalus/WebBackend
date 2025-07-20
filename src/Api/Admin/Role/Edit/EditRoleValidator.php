<?php

namespace Nebalus\Webapi\Api\Admin\Role\Edit;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionValue;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;

class EditRoleValidator extends AbstractValidator
{
    private RoleId $roleId;
    private RoleName $roleName;
    private ?RoleDescription $roleDescription;
    private RoleHexColor $roleColor;
    private RoleAccessLevel $accessLevel;
    private bool $appliesToEveryone;
    private bool $disabled;
    private PermissionRoleLinkCollection $permissionRoleLinks;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "role_id" => RoleId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::object([
                "name" => RoleName::getSchema(),
                "description" => S::nullable(RoleDescription::getSchema()),
                "color" => RoleHexColor::getSchema(),
                "access_level" => RoleAccessLevel::getSchema(),
                "applies_to_everyone" => S::boolean(),
                "disabled" => S::boolean(),
                "permissions" => S::array(S::object([
                    "node" => PermissionNode::getSchema(),
                    "value" => S::nullable(PermissionNode::getSchema()->default(null)),
                    "affects_all_sub_permissions" => S::boolean(),
                ])),
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
        $this->roleName = RoleName::from($bodyData["name"]);
        $this->roleDescription = array_key_exists("description", $bodyData) ? RoleDescription::from($bodyData["description"]) : null;
        $this->roleColor = RoleHexColor::from($bodyData["color"]);
        $this->accessLevel = RoleAccessLevel::from($bodyData["access_level"]);
        $this->appliesToEveryone = $bodyData["applies_to_everyone"];
        $this->disabled = $bodyData["disabled"];
        $this->permissionRoleLinks = PermissionRoleLinkCollection::fromObjects(
            ...array_map(
                fn(array $permission) => PermissionRoleLink::from(
                    PermissionNode::from($permission["node"]),
                    $permission["affects_all_sub_permissions"],
                    array_key_exists("value", $bodyData) ? PermissionValue::from($permission["value"]) : null
                ),
                $bodyData["permissions"]
            )
        );
    }

    public function getRoleId(): RoleId
    {
        return $this->roleId;
    }

    public function getRoleName(): RoleName
    {
        return $this->roleName;
    }

    public function getRoleDescription(): ?RoleDescription
    {
        return $this->roleDescription;
    }

    public function getRoleColor(): RoleHexColor
    {
        return $this->roleColor;
    }

    public function getAccessLevel(): RoleAccessLevel
    {
        return $this->accessLevel;
    }

    public function appliesToEveryone(): bool
    {
        return $this->appliesToEveryone;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getPermissionRoleLinks(): PermissionRoleLinkCollection
    {
        return $this->permissionRoleLinks;
    }
}
