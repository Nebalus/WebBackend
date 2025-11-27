<?php

namespace Nebalus\Webapi\Api\Admin\Role\Create;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;

class CreateRoleValidator extends AbstractValidator
{
    private RoleName $roleName;
    private ?RoleDescription $roleDescription;
    private RoleHexColor $roleColor;
    private RoleAccessLevel $accessLevel;
    private bool $appliesToEveryone;
    private bool $disabled;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::BODY => S::object([
                "name" => RoleName::getSchema(),
                "description" => RoleDescription::getSchema()->nullish(),
                "color" => RoleHexColor::getSchema(),
                "access_level" => RoleAccessLevel::getSchema(),
                "applies_to_everyone" => S::boolean(),
                "disabled" => S::boolean(),
            ]),
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->roleName = RoleName::from($bodyData["name"]);
        $this->roleDescription = isset($bodyData["description"]) ? RoleDescription::from($bodyData["description"]) : null;
        $this->roleColor = RoleHexColor::from($bodyData["color"]);
        $this->accessLevel = RoleAccessLevel::from($bodyData["access_level"]);
        $this->appliesToEveryone = $bodyData["applies_to_everyone"];
        $this->disabled = $bodyData["disabled"];
    }

    public function getRoleName(): RoleName
    {
        return $this->roleName;
    }
    public function getRoleColor(): RoleHexColor
    {
        return $this->roleColor;
    }
    public function getRoleDescription(): ?RoleDescription
    {
        return $this->roleDescription;
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
}
