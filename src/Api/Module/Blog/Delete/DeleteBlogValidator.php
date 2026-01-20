<?php

namespace Nebalus\Webapi\Api\Module\Blog\Delete;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\User\UserId;

class DeleteBlogValidator extends AbstractValidator
{
    private BlogId $blogId;
    private UserId $userId;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'blog_id' => BlogId::getSchema(),
                "user_id" => UserId::getSchema(),
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
    }

    public function getBlogId(): BlogId
    {
        return $this->blogId;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}