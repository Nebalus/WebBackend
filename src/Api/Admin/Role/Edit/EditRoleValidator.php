<?php

namespace Nebalus\Webapi\Api\Admin\Role\Edit;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Api\RequestParamTypes;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;

class EditRoleValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "roleName" => S::string()->min(RoleName::MIN_LENGTH)->max(RoleName::MAX_LENGTH),
            ]),
            RequestParamTypes::BODY => S::object([
                "name" => S::string()->min(RoleName::MIN_LENGTH)->max(RoleName::MAX_LENGTH),
                "applies_to_everyone" => S::boolean()->optional()->default(false),
                "description" => S::string()->optional()->default("test")->max(RoleDescription::MAX_LENGTH)->regex(RoleDescription::REGEX),
                "privileges" => S::array(S::object([
                    "node" => S::string()->min(RoleName::MIN_LENGTH)->max(RoleName::MAX_LENGTH),
                    "value" => S::number()->nonNegative()->nullable()->default(null),
                ])),
            ])
        ]));
    }

    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        var_dump(json_encode($bodyData, JSON_PRETTY_PRINT));
        // TODO: Implement onValidate() method.
    }
}
