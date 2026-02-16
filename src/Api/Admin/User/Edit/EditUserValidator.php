<?php

namespace Nebalus\Webapi\Api\Admin\User\Edit;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserId;
use Nebalus\Webapi\Value\User\Username;

class EditUserValidator extends AbstractValidator
{
    private UserId $userId;
    private Username $username;
    private UserEmail $email;
    private bool $emailVerified;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "user_id" => UserId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::object([
                "username" => Username::getSchema(),
                "email" => UserEmail::getSchema(),
                "email_verified" => S::boolean(),
            ])
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->userId = UserId::from($pathArgsData["user_id"]);
        $this->username = Username::from($bodyData["username"]);
        $this->email = UserEmail::from($bodyData["email"]);
        $this->emailVerified = $bodyData["email_verified"];
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }
}
