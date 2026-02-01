<?php

namespace Nebalus\Webapi\Api\Module\Blog\Get;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\User\UserId;

class GetBlogValidator extends AbstractValidator
{
    private BlogId $blogId;
    private UserId $userId;
    private bool $withContent;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'blog_id' => BlogId::getSchema(),
                "user_id" => UserId::getSchema(),
            ]),
            RequestParamTypes::QUERY_PARAMS => S::object([
                "with_content" => S::boolean()->optional()->default(false)->stringable(),
            ])
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->blogId = BlogId::from($pathArgsData['blog_id']);
        $this->userId = UserId::from($pathArgsData["user_id"]);
        $this->withContent = (bool) $queryParamsData["with_content"];
    }

    public function getBlogId(): BlogId
    {
        return $this->blogId;
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
