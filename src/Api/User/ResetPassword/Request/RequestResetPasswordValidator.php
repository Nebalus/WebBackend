<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ResetPassword\Request;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\UserEmail;

class RequestResetPasswordValidator extends AbstractValidator
{
    private UserEmail $email;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::BODY => S::object([
                'email' => UserEmail::getSchema(),
            ]),
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->email = UserEmail::from($bodyData['email']);
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }
}
