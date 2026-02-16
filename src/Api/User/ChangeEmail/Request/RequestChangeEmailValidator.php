<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeEmail\Request;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserId;

class RequestChangeEmailValidator extends AbstractValidator
{
    private UserId $userId;
    private UserEmail $email;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'user_id' => UserId::getSchema(),
            ]),
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
        $this->userId = UserId::from($pathArgsData['user_id']);
        $this->email = UserEmail::from($bodyData['email']);
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }
}
