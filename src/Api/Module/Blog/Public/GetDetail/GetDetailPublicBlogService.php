<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Analytics\GetPublicDetail;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Result\Result;

readonly class GetDetailPublicBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private GetDetailPublicBlogResponder $responder
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(string $slug): ResultInterface
    {
        $blog = $this->blogRepository->findBlogBySlug(BlogSlug::from($slug));

        if (!$blog) {
            return Result::createError("Blog not found", StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($blog->getBlogStatus() !== BlogStatus::PUBLISHED) {
            return Result::createError("Blog not found", StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $this->responder->render($blog);
    }
}
