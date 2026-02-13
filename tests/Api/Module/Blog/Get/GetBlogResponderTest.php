<?php

namespace UnitTesting\Api\Module\Blog\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Get\GetBlogResponder;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetBlogResponderTest extends TestCase
{
    #[Test]
    public function testRenderWithoutContent(): void
    {
        $responder = new GetBlogResponder();
        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(3);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('my-blog');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('My Blog');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('An excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $content = $this->createMock(BlogContent::class);
        $content->method('asString')->willReturn('Full content');
        $blog->method('getBlogContent')->willReturn($content);

        $blog->method('getBlogStatus')->willReturn(BlogStatus::DRAFT);
        $blog->method('isFeatured')->willReturn(false);
        $blog->method('getPublishedAt')->willReturn(null);
        $blog->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));
        $blog->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));

        $result = $responder->render($blog, false);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Blog Fetched', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals(3, $data['blog_id']);
        $this->assertEquals('my-blog', $data['slug']);
        $this->assertArrayNotHasKey('content', $data);
    }

    #[Test]
    public function testRenderWithContent(): void
    {
        $responder = new GetBlogResponder();
        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(3);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('my-blog');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('My Blog');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('An excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $content = $this->createMock(BlogContent::class);
        $content->method('asString')->willReturn('Full content');
        $blog->method('getBlogContent')->willReturn($content);

        $blog->method('getBlogStatus')->willReturn(BlogStatus::PUBLISHED);
        $blog->method('isFeatured')->willReturn(true);
        $blog->method('getPublishedAt')->willReturn(new \DateTimeImmutable('2025-03-01'));
        $blog->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));
        $blog->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2025-03-01'));

        $result = $responder->render($blog, true);

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertArrayHasKey('content', $data);
        $this->assertEquals('Full content', $data['content']);
    }
}
