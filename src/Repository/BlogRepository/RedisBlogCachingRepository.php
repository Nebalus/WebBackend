<?php

namespace Nebalus\Webapi\Repository\BlogRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\User\UserId;
use Redis;
use RedisException;

readonly class RedisBlogCachingRepository
{
    public const string HASH_KEY = 'blogs';
    public const string OWNER_INDEX_KEY = 'blogs:owner:';

    public function __construct(
        private Redis $redis,
    ) {
    }

    public function addBlog(BlogPost $blog): void
    {
        try {
            $blogData = json_encode($blog->asArray());
            $this->redis->hset(
                self::HASH_KEY,
                (string) $blog->getBlogId()->asInt(),
                $blogData
            );

            // Index by owner for quick owner-based lookups
            $this->redis->sadd(
                self::OWNER_INDEX_KEY . $blog->getOwnerId()->asInt(),
                (string) $blog->getBlogId()->asInt()
            );
        } catch (RedisException) {
        }
    }

    public function getBlog(BlogId $blogId): ?BlogPost
    {
        try {
            $blogData = $this->redis->hget(self::HASH_KEY, (string) $blogId->asInt());
            if ($blogData) {
                $dataArray = json_decode($blogData, true);
                return BlogPost::fromArray($dataArray);
            }
        } catch (RedisException | ApiException) {
        }
        return null;
    }

    public function updateBlog(BlogPost $blog): bool
    {
        try {
            $existingBlog = $this->getBlog($blog->getBlogId());
            if ($existingBlog) {
                $this->addBlog($blog);
                return true;
            }
        } catch (RedisException) {
        }
        return false;
    }

    public function deleteBlog(BlogId $blogId): void
    {
        try {
            $blog = $this->getBlog($blogId);
            if ($blog) {
                $this->redis->hdel(self::HASH_KEY, (string) $blogId->asInt());
                $this->redis->srem(
                    self::OWNER_INDEX_KEY . $blog->getOwnerId()->asInt(),
                    (string) $blogId->asInt()
                );
            }
        } catch (RedisException) {
        }
    }

    public function getAllBlogsFromOwner(UserId $ownerId): BlogPostCollection
    {
        $blogs = [];
        try {
            $blogIds = $this->redis->smembers(self::OWNER_INDEX_KEY . $ownerId->asInt());
            foreach ($blogIds as $blogId) {
                $blogData = $this->redis->hget(self::HASH_KEY, (string) $blogId);
                if ($blogData) {
                    $blogs[] = BlogPost::fromArray(json_decode($blogData, true));
                }
            }
        } catch (RedisException | ApiException) {
        }

        return BlogPostCollection::fromObjects(...$blogs);
    }

    public function deleteAllBlogs(): void
    {
        try {
            // Get all blog data to find owner keys
            $allBlogs = $this->redis->hgetall(self::HASH_KEY);
            foreach ($allBlogs as $blogData) {
                $data = json_decode($blogData, true);
                if (isset($data['owner_id'])) {
                    $this->redis->del([self::OWNER_INDEX_KEY . $data['owner_id']]);
                }
            }
            $this->redis->del([self::HASH_KEY]);
        } catch (RedisException) {
        }
    }
}