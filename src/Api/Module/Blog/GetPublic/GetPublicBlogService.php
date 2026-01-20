<?php

namespace Nebalus\Webapi\Api\Module\Blog\GetPublic;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;

readonly class GetPublicBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private GetPublicBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetPublicBlogValidator $validator): ResultInterface
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
