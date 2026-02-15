<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Published\GetAll;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;

readonly class GetAllPublishedBlogsService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private GetAllPublishedBlogsResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetAllPublishedBlogsValidator $validator): ResultInterface
    {
        $blogs = $this->blogRepository->findPublishedBlogs(
            $validator->getPerPage(),
            $validator->getOffset()
        );

        $totalCount = $this->blogRepository->countPublishedBlogs();

        return $this->responder->render(
            $blogs,
            $validator->getPage(),
            $validator->getPerPage(),
            $totalCount
        );
    }
}
