<?php

namespace UnitTesting\Api\Module\Blog\GetPublic;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Published\GetAll\GetAllPublishedBlogsResponder;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetPublicBlogResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsPaginatedResponse(): void
    {
        $responder = new GetAllPublishedBlogsResponder();

        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(1);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('public-blog');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('Public Blog');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('Public excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $blog->method('isFeatured')->willReturn(true);
        $blog->method('getPublishedAt')->willReturn(new \DateTimeImmutable('2025-02-01'));

        $blogs = BlogPostCollection::fromObjects($blog);

        $result = $responder->render($blogs, 1, 10, 25);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Public blogs retrieved', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertArrayHasKey('blogs', $data);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertCount(1, $data['blogs']);
        $this->assertEquals(1, $data['pagination']['page']);
        $this->assertEquals(10, $data['pagination']['per_page']);
        $this->assertEquals(25, $data['pagination']['total_count']);
        $this->assertEquals(3, $data['pagination']['total_pages']);
    }

    #[Test]
    public function testRenderReturnsEmptyBlogsWithPagination(): void
    {
        $responder = new GetAllPublishedBlogsResponder();
        $blogs = BlogPostCollection::fromObjects();

        $result = $responder->render($blogs, 1, 10, 0);

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(0, $data['blogs']);
        $this->assertEquals(0, $data['pagination']['total_count']);
        $this->assertEquals(0, $data['pagination']['total_pages']);
    }
}
