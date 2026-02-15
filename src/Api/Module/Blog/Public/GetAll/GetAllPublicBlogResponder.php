<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Public\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\Result\Result;

class GetAllPublicBlogResponder
{
    public function render(
        BlogPostCollection $blogs,
        int $page,
        int $perPage,
        int $totalCount
    ): ResultInterface {
        $fields = [];
        foreach ($blogs as $blog) {
            $fields[] = [
                "blog_id" => $blog->getBlogId()->asInt(),
                "slug" => $blog->getBlogSlug()->asString(),
                "title" => $blog->getBlogTitle()->asString(),
                "excerpt" => $blog->getBlogExcerpt()->asString(),
                "is_featured" => $blog->isFeatured(),
                "published_at" => $blog->getPublishedAt()?->format(DATE_ATOM),
            ];
        }

        $totalPages = (int) ceil($totalCount / $perPage);

        $payload = [
            "blogs" => $fields,
            "pagination" => [
                "page" => $page,
                "per_page" => $perPage,
                "total_count" => $totalCount,
                "total_pages" => $totalPages,
            ]
        ];

        return Result::createSuccess("Public blogs retrieved", StatusCodeInterface::STATUS_OK, $payload);
    }
}
