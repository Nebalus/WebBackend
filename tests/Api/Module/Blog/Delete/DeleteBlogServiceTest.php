<?php

namespace UnitTesting\Api\Module\Blog\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Delete\DeleteBlogResponder;
use Nebalus\Webapi\Api\Module\Blog\Delete\DeleteBlogService;
use Nebalus\Webapi\Api\Module\Blog\Delete\DeleteBlogValidator;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteBlogServiceTest extends TestCase
{
    private MySqlBlogRepository $blogRepository;
    private DeleteBlogResponder $responder;
    private DeleteBlogService $service;
    private DeleteBlogValidator $validator;

    protected function setUp(): void
    {
        $this->blogRepository = $this->createMock(MySqlBlogRepository::class);
        $this->responder = $this->createMock(DeleteBlogResponder::class);
        $this->service = new DeleteBlogService($this->blogRepository, $this->responder);
        $this->validator = $this->createMock(DeleteBlogValidator::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionWhenUserHasNoAccess(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $requestingUserId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($requestingUserId);

        $validatorUserId = $this->createMock(UserId::class);
        $this->validator->method('getUserId')->willReturn($validatorUserId);
        $validatorUserId->method('equals')->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteDeletesBlogWhenSelfUserHasPermission(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $this->validator->method('getUserId')->willReturn($userId);
        $userId->method('equals')->willReturn(true);

        $blogId = $this->createMock(BlogId::class);
        $this->validator->method('getBlogId')->willReturn($blogId);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $this->blogRepository->expects($this->once())
            ->method('deleteBlogByIdFromOwner')
            ->with($userId, $blogId)
            ->willReturn(true);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
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

        $blogId = $this->createMock(BlogId::class);
        $this->validator->method('getBlogId')->willReturn($blogId);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $this->blogRepository->method('deleteBlogByIdFromOwner')->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
    }
}
