<?php

namespace Nebalus\Webapi\Api\User\Auth;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\Authentication\Totp\TOTPCode;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use Nebalus\Webapi\Value\User\Username;

class AuthUserValidator extends AbstractValidator
{
    private Username $username;
    private string $password;
    private bool $rememberMe;
    private ?TOTPCode $TOTPCode;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::BODY => S::object([
                'username' => Username::getSchema(),
                'password' => UserPassword::getSchema(),
                'remember_me' => S::boolean()->default(false),
                'totp' => TOTPCode::getSchema()->optional(),
            ])
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->username = Username::from($bodyData['username']);
        $this->password = $bodyData['password'];
        $this->rememberMe = $bodyData['remember_me'];
        $this->TOTPCode = array_key_exists("totp", $bodyData) ? TOTPCode::from($bodyData['totp']) : null;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRememberMe(): bool
    {
        return $this->rememberMe;
    }

    public function getTOTPCode(): ?TOTPCode
    {
        return $this->TOTPCode;
    }
}
