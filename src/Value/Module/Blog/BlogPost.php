<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\Module\Blog;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\UserId;

readonly class BlogPost
{
    private function __construct(
        private BlogId $blogId,
        private UserId $ownerId,
        private BlogSlug $blogSlug,
        private BlogExcerpt $blogExcerpt,
        private BlogTitle $blogTitle,
        private BlogContent $blogContent,
        private BlogStatus $blogStatus,
        private bool $isFeatured,
        private ?DateTimeImmutable $publishedAt,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        try {
            $createdAt = new DateTimeImmutable($data["created_at"]);
            $updatedAt = new DateTimeImmutable($data["updated_at"]);
            $publishedAt = empty($data["published_at"]) ? null : new DateTimeImmutable($data["published_at"]);
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }

        $blogId = BlogId::from($data["blog_id"]);
        $ownerId = UserId::from($data["owner_id"]);
        $blogSlug = BlogSlug::from($data["slug"]);
        $blogExcerpt = BlogExcerpt::from($data["excerpt"]);
        $blogTitle = BlogTitle::from($data["title"]);
        $blogContent = BlogContent::from($data["content"]);
        $blogStatus = BlogStatus::from($data["status"]);
        $isFeatured = (bool) $data["is_featured"];

        return new self(
            $blogId,
            $ownerId,
            $blogSlug,
            $blogExcerpt,
            $blogTitle,
            $blogContent,
            $blogStatus,
            $isFeatured,
            $publishedAt,
            $createdAt,
            $updatedAt
        );
    }

    public function asArray(): array
    {
        return [
            "blog_id" => $this->blogId->asInt(),
            "owner_id" => $this->ownerId->asInt(),
            "slug" => $this->blogSlug->asString(),
            "excerpt" => $this->blogExcerpt->asString(),
            "title" => $this->blogTitle->asString(),
            "content" => $this->blogContent->asString(),
            "status" => $this->blogStatus->value,
            "is_featured" => $this->isFeatured,
            "published_at" => $this->publishedAt?->format(DATE_ATOM),
            "created_at" => $this->createdAt->format(DATE_ATOM),
            "updated_at" => $this->updatedAt->format(DATE_ATOM),
        ];
    }

    public function getBlogId(): BlogId
    {
        return $this->blogId;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getBlogSlug(): BlogSlug
    {
        return $this->blogSlug;
    }

    public function getBlogExcerpt(): BlogExcerpt
    {
        return $this->blogExcerpt;
    }

    public function getBlogTitle(): BlogTitle
    {
        return $this->blogTitle;
    }

    public function getBlogContent(): BlogContent
    {
        return $this->blogContent;
    }

    public function getBlogStatus(): BlogStatus
    {
        return $this->blogStatus;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
