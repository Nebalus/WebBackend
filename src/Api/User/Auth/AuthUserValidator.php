<?php

namespace Nebalus\Webapi\Api\User\Auth;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Value\Internal\Validation\ValidatedData;
use Nebalus\Webapi\Value\User\Totp\TOTPCode;
use Nebalus\Webapi\Value\User\Username;

class AuthUserValidator extends AbstractValidator
{
    private Username $username;
    private string $password;
    private bool $rememberMe;
    private ?TOTPCode $TOTPCode;

    public function __construct()
    {
        $rules = [
            'body' => S::object([
                'username' => S::string(),
                'password' => S::string(),
                'remember_me' => S::boolean()->optional()->default(false),
                'totp' => S::string()->optional()->nullable(),
            ])
        ];
        parent::__construct($rules);
    }

    protected function onValidate(ValidatedData $validatedData): void
    {
        $this->username = Username::from($validatedData->getBodyData()['username']);
        $this->password = $validatedData->getBodyData()['password'];
        $this->rememberMe = $validatedData->getBodyData()['remember_me'];
        $this->TOTPCode = is_null($validatedData->getBodyData()['totp']) ? null : TOTPCode::from($validatedData->getBodyData()['totp']);
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
