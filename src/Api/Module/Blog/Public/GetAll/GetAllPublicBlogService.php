<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Public\GetAll;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;

readonly class GetAllPublicBlogService
{
    public function __construct(
        private MySqlBlogRepository       $blogRepository,
        private GetAllPublicBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetAllPublicBlogValidator $validator): ResultInterface
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
