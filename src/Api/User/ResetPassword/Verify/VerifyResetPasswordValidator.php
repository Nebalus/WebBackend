<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ResetPassword\Verify;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;

class VerifyResetPasswordValidator extends AbstractValidator
{
    private string $token;
    private string $newPassword;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'token' => S::string()->min(1),
            ]),
            RequestParamTypes::BODY => S::object([
                'new_password' => UserPassword::getSchema(),
            ]),
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->token = $pathArgsData['token'];
        $this->newPassword = $bodyData['new_password'];
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
