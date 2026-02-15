<?php

namespace UnitTesting\Api\Module\Blog\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Create\CreateBlogResponder;
use Nebalus\Webapi\Api\Module\Blog\Create\CreateBlogService;
use Nebalus\Webapi\Api\Module\Blog\Create\CreateBlogValidator;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Module\Blog\BlogPost;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateBlogServiceTest extends TestCase
{
    private MySqlBlogRepository $blogRepository;
    private CreateBlogResponder $responder;
    private CreateBlogService $service;
    private CreateBlogValidator $validator;

    protected function setUp(): void
    {
        $this->blogRepository = $this->createMock(MySqlBlogRepository::class);
        $this->responder = $this->createMock(CreateBlogResponder::class);
        $this->service = new CreateBlogService($this->blogRepository, $this->responder);
        $this->validator = $this->createMock(CreateBlogValidator::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionWhenUserHasNoAccess(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteCreatesBlogAndReturnsResultWhenAuthorized(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $slug = $this->createMock(BlogSlug::class);
        $this->validator->method('getSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $this->validator->method('getTitle')->willReturn($title);

        $content = $this->createMock(BlogContent::class);
        $this->validator->method('getContent')->willReturn($content);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $this->validator->method('getExcerpt')->willReturn($excerpt);

        $this->validator->method('getStatus')->willReturn(BlogStatus::DRAFT);
        $this->validator->method('isFeatured')->willReturn(false);
        $this->validator->method('getImageBannerId')->willReturn(null);

        $this->blogRepository->expects($this->once())
            ->method('insertBlog')
            ->with($userId, $slug, null, $title, $content, $excerpt, BlogStatus::DRAFT, false);

        $blogId = $this->createMock(BlogId::class);
        $this->blogRepository->method('getLastInsertedBlogId')->willReturn($blogId);

        $blog = $this->createMock(BlogPost::class);
        $this->blogRepository->method('findBlogById')->with($blogId)->willReturn($blog);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($blog)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
