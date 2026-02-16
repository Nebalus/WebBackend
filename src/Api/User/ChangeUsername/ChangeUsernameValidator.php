<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\ChangeUsername;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\UserId;
use Nebalus\Webapi\Value\User\Username;

class ChangeUsernameValidator extends AbstractValidator
{
    private UserId $userId;
    private Username $username;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'user_id' => UserId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::object([
                'username' => Username::getSchema(),
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
        $this->username = Username::from($bodyData['username']);
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }
}
