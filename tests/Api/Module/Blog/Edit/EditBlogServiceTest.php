<?php

namespace UnitTesting\Api\Module\Blog\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Edit\EditBlogResponder;
use Nebalus\Webapi\Api\Module\Blog\Edit\EditBlogService;
use Nebalus\Webapi\Api\Module\Blog\Edit\EditBlogValidator;
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

class EditBlogServiceTest extends TestCase
{
    private MySqlBlogRepository $blogRepository;
    private EditBlogResponder $responder;
    private EditBlogService $service;
    private EditBlogValidator $validator;

    protected function setUp(): void
    {
        $this->blogRepository = $this->createMock(MySqlBlogRepository::class);
        $this->responder = $this->createMock(EditBlogResponder::class);
        $this->service = new EditBlogService($this->blogRepository, $this->responder);
        $this->validator = $this->createMock(EditBlogValidator::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionWhenSelfUserHasNoAccess(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $this->validator->method('getUserId')->willReturn($userId);
        $userId->method('equals')->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteUpdatesBlogWhenSelfUserHasPermission(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $this->validator->method('getUserId')->willReturn($userId);
        $userId->method('equals')->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $blogId = $this->createMock(BlogId::class);
        $this->validator->method('getBlogId')->willReturn($blogId);

        $slug = $this->createMock(BlogSlug::class);
        $this->validator->method('getSlug')->willReturn($slug);

        $title = $this->createMock(BlogTitle::class);
        $this->validator->method('getTitle')->willReturn($title);

        $content = $this->createMock(BlogContent::class);
        $this->validator->method('getContent')->willReturn($content);

        $excerpt = $this->createMock(BlogExcerpt::class);
        $this->validator->method('getExcerpt')->willReturn($excerpt);

        $this->validator->method('getStatus')->willReturn(BlogStatus::PUBLISHED);
        $this->validator->method('isFeatured')->willReturn(true);
        $this->validator->method('getImageBannerId')->willReturn(null);

        $blog = $this->createMock(BlogPost::class);
        $blog->method('getOwnerId')->willReturn($userId);
        $userId->method('equals')->willReturn(true);

        $this->blogRepository->method('updateBlogFromOwner')->willReturn($blog);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($blog)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }

    #[Test]
    public function testExecuteReturnsNotFoundWhenBlogDoesNotExist(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $this->validator->method('getUserId')->willReturn($userId);
        $userId->method('equals')->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $blogId = $this->createMock(BlogId::class);
        $this->validator->method('getBlogId')->willReturn($blogId);

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

        $this->blogRepository->method('updateBlogFromOwner')->willReturn(null);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
    }
}
