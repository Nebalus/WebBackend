<?php

namespace UnitTesting\Api\Module\Blog\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Create\CreateBlogResponder;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateBlogResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new CreateBlogResponder();
        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(1);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('test-slug');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('Test Title');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('Test excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $content = $this->createMock(BlogContent::class);
        $content->method('asString')->willReturn('Test content');
        $blog->method('getBlogContent')->willReturn($content);

        $blog->method('getBlogStatus')->willReturn(BlogStatus::DRAFT);
        $blog->method('isFeatured')->willReturn(false);
        $blog->method('getPublishedAt')->willReturn(null);
        $blog->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));
        $blog->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2025-01-02'));

        $result = $responder->render($blog);

        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $result->getStatusCode());
        $this->assertEquals('Blog Created', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals(1, $data['blog_id']);
        $this->assertEquals('test-slug', $data['slug']);
        $this->assertEquals('Test Title', $data['title']);
        $this->assertEquals('Test excerpt', $data['excerpt']);
        $this->assertEquals('Test content', $data['content']);
        $this->assertEquals('DRAFT', $data['status']);
        $this->assertFalse($data['is_featured']);
    }
}
