<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\Result\Result;

class GetAllBlogResponder
{
    public function render(BlogPostCollection $blogs, bool $withContent): ResultInterface
    {
        $fields = [];
        foreach ($blogs as $blog) {
            $fields[] = [
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
                $fields[count($fields) - 1]["content"] = $blog->getBlogContent()->asString();
            }
        }

        return Result::createSuccess("List of blogs found", StatusCodeInterface::STATUS_OK, $fields);
    }
}
