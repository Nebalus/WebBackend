<?php

namespace UnitTesting\Api\Module\Blog\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Edit\EditBlogResponder;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EditBlogResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new EditBlogResponder();
        $blog = $this->createMock(BlogPost::class);

        $blogId = $this->createMock(BlogId::class);
        $blogId->method('asInt')->willReturn(5);
        $blog->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $slug->method('asString')->willReturn('updated-slug');
        $blog->method('getBlogSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $title->method('asString')->willReturn('Updated Title');
        $blog->method('getBlogTitle')->willReturn($title);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $excerpt->method('asString')->willReturn('Updated excerpt');
        $blog->method('getBlogExcerpt')->willReturn($excerpt);

        $content = $this->createMock(BlogContent::class);
        $content->method('asString')->willReturn('Updated content');
        $blog->method('getBlogContent')->willReturn($content);

        $blog->method('getBlogStatus')->willReturn(BlogStatus::PUBLISHED);
        $blog->method('isFeatured')->willReturn(true);
        $blog->method('getPublishedAt')->willReturn(new \DateTimeImmutable('2025-06-01'));
        $blog->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2025-01-01'));
        $blog->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2025-06-01'));

        $result = $responder->render($blog);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Blog Updated', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals(5, $data['blog_id']);
        $this->assertEquals('updated-slug', $data['slug']);
        $this->assertEquals('Updated Title', $data['title']);
        $this->assertEquals('Updated content', $data['content']);
        $this->assertEquals('PUBLISHED', $data['status']);
        $this->assertTrue($data['is_featured']);
    }
}
