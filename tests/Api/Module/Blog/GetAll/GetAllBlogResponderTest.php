<?php

namespace UnitTesting\Api\Module\Blog\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\GetAll\GetAllBlogResponder;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllBlogResponderTest extends TestCase
{
    #[Test]
    public function testRenderWithoutContent(): void
    {
        $responder = new GetAllBlogResponder();

        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(1);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('test-blog');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('Test Blog');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('Excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $content = $this->createMock(BlogContent::class);
        $content->method('asString')->willReturn('Content');
        $blog->method('getBlogContent')->willReturn($content);

        $blog->method('getBlogStatus')->willReturn(BlogStatus::DRAFT);
        $blog->method('isFeatured')->willReturn(false);
        $blog->method('getPublishedAt')->willReturn(null);
        $blog->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));
        $blog->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));

        $blogs = BlogPostCollection::fromObjects($blog);

        $result = $responder->render($blogs, false);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('List of blogs found', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(1, $data);
        $this->assertArrayNotHasKey('content', $data[0]);
    }

    #[Test]
    public function testRenderWithContent(): void
    {
        $responder = new GetAllBlogResponder();

        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(1);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('test-blog');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('Test Blog');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('Excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $content = $this->createMock(BlogContent::class);
        $content->method('asString')->willReturn('Full Content');
        $blog->method('getBlogContent')->willReturn($content);

        $blog->method('getBlogStatus')->willReturn(BlogStatus::PUBLISHED);
        $blog->method('isFeatured')->willReturn(true);
        $blog->method('getPublishedAt')->willReturn(new \DateTimeImmutable('2025-02-01'));
        $blog->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));
        $blog->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2025-02-01'));

        $blogs = BlogPostCollection::fromObjects($blog);

        $result = $responder->render($blogs, true);

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('content', $data[0]);
        $this->assertEquals('Full Content', $data[0]['content']);
    }

    #[Test]
    public function testRenderEmptyCollection(): void
    {
        $responder = new GetAllBlogResponder();
        $blogs = BlogPostCollection::fromObjects();

        $result = $responder->render($blogs, false);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(0, $data);
    }
}
