<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Verify;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;

class VerifyChangeEmailValidator extends AbstractValidator
{
    private string $token;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'token' => S::string()->min(1),
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
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
