<?php

namespace Nebalus\Webapi\Api\Module\Blog\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Result\Result;

class GetBlogResponder
{
    public function render(BlogPost $blog, bool $withContent): ResultInterface
    {
        $fields = [
            "blog_id" => $blog->getBlogId()->asInt(),
            "slug" => $blog->getBlogSlug()->asString(),
            "title" => $blog->getBlogTitle()->asString(),
            "excerpt" => $blog->getBlogExcerpt()->asString(),
            "status" => $blog->getBlogStatus()->value,
            "is_featured" => $blog->isFeatured(),
            "published_at" => $blog->getPublishedAt()?->format(DATE_ATOM),
            "created_at" => $blog->getCreatedAt()->format(DATE_ATOM),
            "updated_at" => $blog->getUpdatedAt()->format(DATE_ATOM),
        ];

        if ($withContent) {
            $fields["content"] = $blog->getBlogContent()->asString();
        }

        return Result::createSuccess("Blog Fetched", StatusCodeInterface::STATUS_OK, $fields);
    }
}
