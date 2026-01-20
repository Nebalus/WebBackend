<?php

namespace Nebalus\Webapi\Api\Module\Blog\GetAll;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Value\User\UserId;

class GetAllBlogValidator extends AbstractValidator
{
    private UserId $userId;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "user_id" => UserId::getSchema(),
            ])
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->userId = UserId::from($pathArgsData["user_id"]);
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
