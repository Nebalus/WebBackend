<?php

namespace Nebalus\Webapi\Api\Module\Blog\Edit;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use Nebalus\Webapi\Value\User\UserId;

class EditBlogValidator extends AbstractValidator
{
    private UserId $userId;
    private BlogId $blogId;
    private BlogSlug $slug;
    private BlogTitle $title;
    private BlogContent $content;
    private BlogExcerpt $excerpt;
    private BlogStatus $status;
    private bool $isFeatured;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "user_id" => UserId::getSchema(),
                "blog_id" => BlogId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::object([
                'slug' => BlogSlug::getSchema(),
                'title' => BlogTitle::getSchema(),
                'content' => BlogContent::getSchema(),
                'excerpt' => BlogExcerpt::getSchema(),
                'status' => S::string()->optional()->default(BlogStatus::DRAFT->value),
                'is_featured' => S::boolean()->optional()->default(false),
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
        $this->blogId = BlogId::from($pathArgsData['blog_id']);
        $this->slug = BlogSlug::from($bodyData['slug']);
        $this->title = BlogTitle::from($bodyData['title']);
        $this->content = BlogContent::from($bodyData['content']);
        $this->excerpt = BlogExcerpt::from($bodyData['excerpt']);
        $this->status = BlogStatus::from($bodyData['status']);
        $this->isFeatured = $bodyData['is_featured'];
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getBlogId(): BlogId
    {
        return $this->blogId;
    }

    public function getSlug(): BlogSlug
    {
        return $this->slug;
    }

    public function getTitle(): BlogTitle
    {
        return $this->title;
    }

    public function getContent(): BlogContent
    {
        return $this->content;
    }

    public function getExcerpt(): BlogExcerpt
    {
        return $this->excerpt;
    }

    public function getStatus(): BlogStatus
    {
        return $this->status;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }
}