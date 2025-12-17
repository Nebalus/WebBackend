<?php

namespace Nebalus\Webapi\Api\User\Register;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationTokenValue;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\Username;

class RegisterUserValidator extends AbstractValidator
{
    private InvitationTokenValue $pureInvitationToken;
    private UserEmail $userEmail;
    private Username $username;
    private UserPassword $userPassword;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::BODY => S::object([
                'invitation_token' => InvitationTokenValue::getSchema(),
                'email' => UserEmail::getSchema(),
                'username' => UserName::getSchema(),
                'password' => UserPassword::getSchema(),
            ])
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->pureInvitationToken = InvitationTokenValue::from($bodyData["invitation_token"]);
        $this->userEmail = UserEmail::from($bodyData["email"]);
        $this->username = UserName::from($bodyData["username"]);
        $this->userPassword = UserPassword::fromPlain($bodyData["password"]);
    }

    public function getPureInvitationToken(): InvitationTokenValue
    {
        return $this->pureInvitationToken;
    }

    public function getUserEmail(): UserEmail
    {
        return $this->userEmail;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getUserPassword(): UserPassword
    {
        return $this->userPassword;
    }
}
