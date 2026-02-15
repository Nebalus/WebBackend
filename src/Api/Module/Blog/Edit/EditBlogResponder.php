<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Result\Result;

class EditBlogResponder
{
    public function render(BlogPost $blog): ResultInterface
    {
        $fields = [
            "blog_id" => $blog->getBlogId()->asInt(),
            "slug" => $blog->getBlogSlug()->asString(),
            "title" => $blog->getBlogTitle()->asString(),
            "excerpt" => $blog->getBlogExcerpt()->asString(),
            "content" => $blog->getBlogContent()->asString(),
            "status" => $blog->getBlogStatus()->value,
            "is_featured" => $blog->isFeatured(),
            "published_at" => $blog->getPublishedAt()?->format(DATE_ATOM),
            "created_at" => $blog->getCreatedAt()->format(DATE_ATOM),
            "updated_at" => $blog->getUpdatedAt()->format(DATE_ATOM),
        ];

        return Result::createSuccess("Blog Updated", StatusCodeInterface::STATUS_OK, $fields);
    }
}
