<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\GetAll;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Value\User\UserId;

class GetAllBlogValidator extends AbstractValidator
{
    private UserId $userId;
    private bool $withContent;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "user_id" => UserId::getSchema(),
            ]),
            RequestParamTypes::QUERY_PARAMS => S::object([
                "with_content" => S::boolean()->optional()->default(false)->stringable(),
            ])
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->userId = UserId::from($pathArgsData["user_id"]);
        $this->withContent = (bool) $queryParamsData["with_content"];
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function withContent(): bool
    {
        return $this->withContent;
    }
}
