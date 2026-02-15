<?php

namespace UnitTesting\Api\Module\Blog\GetPublic;

use Nebalus\Webapi\Api\Module\Blog\Published\GetAll\GetAllPublishedBlogsResponder;
use Nebalus\Webapi\Api\Module\Blog\Published\GetAll\GetAllPublishedBlogsService;
use Nebalus\Webapi\Api\Module\Blog\Published\GetAll\GetAllPublishedBlogsValidator;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetPublicBlogServiceTest extends TestCase
{
    private MySqlBlogRepository $blogRepository;
    private GetAllPublishedBlogsResponder $responder;
    private GetAllPublishedBlogsService $service;
    private GetAllPublishedBlogsValidator $validator;

    protected function setUp(): void
    {
        $this->blogRepository = $this->createMock(MySqlBlogRepository::class);
        $this->responder = $this->createMock(GetAllPublishedBlogsResponder::class);
        $this->service = new GetAllPublishedBlogsService($this->blogRepository, $this->responder);
        $this->validator = $this->createMock(GetAllPublishedBlogsValidator::class);
    }

    #[Test]
    public function testExecuteReturnsPublishedBlogsWithPagination(): void
    {
        $this->validator->method('getPerPage')->willReturn(10);
        $this->validator->method('getOffset')->willReturn(0);
        $this->validator->method('getPage')->willReturn(1);

        $blogs = $this->createMock(BlogPostCollection::class);
        $this->blogRepository->method('findPublishedBlogs')
            ->with(10, 0)
            ->willReturn($blogs);

        $this->blogRepository->method('countPublishedBlogs')->willReturn(25);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($blogs, 1, 10, 25)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator);

        $this->assertSame($expectedResult, $result);
    }

    #[Test]
    public function testExecuteWithSecondPage(): void
    {
        $this->validator->method('getPerPage')->willReturn(5);
        $this->validator->method('getOffset')->willReturn(5);
        $this->validator->method('getPage')->willReturn(2);

        $blogs = $this->createMock(BlogPostCollection::class);
        $this->blogRepository->method('findPublishedBlogs')
            ->with(5, 5)
            ->willReturn($blogs);

        $this->blogRepository->method('countPublishedBlogs')->willReturn(12);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($blogs, 2, 5, 12)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator);

        $this->assertSame($expectedResult, $result);
    }
}
