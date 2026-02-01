<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\BlogRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use Nebalus\Webapi\Value\User\UserId;
use PDO;

readonly class MySqlBlogRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function insertBlog(
        UserId $ownerId,
        BlogSlug $slug,
        ?int $imageBannerId,
        BlogTitle $title,
        BlogContent $content,
        BlogExcerpt $excerpt,
        BlogStatus $status,
        bool $isFeatured
    ): bool {
        $sql = <<<SQL
            INSERT INTO blogs
                (owner_id, slug, image_banner_id, title, content, excerpt, status, is_featured)
            VALUES 
                (:owner_id, :slug, :image_banner_id, :title, :content, :excerpt, :status, :is_featured)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->bindValue(':slug', $slug->asString());
        $stmt->bindValue(':image_banner_id', $imageBannerId, $imageBannerId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':title', $title->asString());
        $stmt->bindValue(':content', $content->asString());
        $stmt->bindValue(':excerpt', $excerpt->asString());
        $stmt->bindValue(':status', $status->value);
        $stmt->bindValue(':is_featured', $isFeatured, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function getLastInsertedBlogId(): BlogId
    {
        return BlogId::from((int) $this->pdo->lastInsertId());
    }

    /**
     * @throws ApiException
     */
    public function findBlogBySlug(BlogSlug $blogSlug): ?BlogPost
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM blogs 
            WHERE 
                slug = :slug
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $blogSlug->asString());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return BlogPost::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findBlogById(BlogId $blogId): ?BlogPost
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM blogs 
            WHERE 
                blog_id = :blog_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':blog_id', $blogId->asInt());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return BlogPost::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findBlogsFromOwner(UserId $ownerId): BlogPostCollection
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM blogs
            WHERE
                owner_id = :owner_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = BlogPost::fromArray($row);
        }

        return BlogPostCollection::fromObjects(...$data);
    }

    /**
     * @throws ApiException
     */
    public function updateBlogFromOwner(
        UserId $ownerId,
        BlogId $blogId,
        BlogSlug $slug,
        ?int $imageBannerId,
        BlogTitle $title,
        BlogContent $content,
        BlogExcerpt $excerpt,
        BlogStatus $status,
        bool $isFeatured
    ): ?BlogPost {
        $sql = <<<SQL
            UPDATE blogs 
            SET 
                image_banner_id = :image_banner_id,
                title = :title,
                content = :content,
                excerpt = :excerpt,
                status = :status,
                is_featured = :is_featured,
                slug = :slug,
                updated_at = NOW()
            WHERE 
                owner_id = :owner_id
                AND blog_id = :blog_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':image_banner_id', $imageBannerId, $imageBannerId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':title', $title->asString());
        $stmt->bindValue(':content', $content->asString());
        $stmt->bindValue(':excerpt', $excerpt->asString());
        $stmt->bindValue(':status', $status->value);
        $stmt->bindValue(':is_featured', $isFeatured, PDO::PARAM_BOOL);
        $stmt->bindValue(':slug', $slug->asString());
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->bindValue(':blog_id', $blogId->asInt());
        $stmt->execute();

        return $this->findBlogBySlug($slug);
    }

    public function deleteBlogBySlugFromOwner(UserId $ownerId, BlogSlug $slug): bool
    {
        $sql = <<<SQL
            DELETE FROM blogs 
            WHERE 
                owner_id = :owner_id 
                AND slug = :slug
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->bindValue(':slug', $slug->asString());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function deleteBlogByIdFromOwner(UserId $ownerId, BlogId $blogId): bool
    {
        $sql = <<<SQL
            DELETE FROM blogs 
            WHERE 
                owner_id = :owner_id 
                AND blog_id = :blog_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->bindValue(':blog_id', $blogId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    /**
     * @throws ApiException
     */
    public function findPublishedBlogs(int $limit = 10, int $offset = 0): BlogPostCollection
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM blogs 
            WHERE 
                status = 'PUBLISHED'
            ORDER BY published_at DESC
            LIMIT :limit OFFSET :offset
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = BlogPost::fromArray($row);
        }

        return BlogPostCollection::fromObjects(...$data);
    }

    /**
     * Get total count of published blogs for pagination
     */
    public function countPublishedBlogs(): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) as total
            FROM blogs 
            WHERE status = 'PUBLISHED'
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}