<?php

namespace UnitTesting\Api\Module\Blog\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\GetAll\GetAllBlogResponder;
use Nebalus\Webapi\Api\Module\Blog\GetAll\GetAllBlogService;
use Nebalus\Webapi\Api\Module\Blog\GetAll\GetAllBlogValidator;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogPostCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllBlogServiceTest extends TestCase
{
    private MySqlBlogRepository $blogRepository;
    private GetAllBlogResponder $responder;
    private GetAllBlogService $service;
    private GetAllBlogValidator $validator;

    protected function setUp(): void
    {
        $this->blogRepository = $this->createMock(MySqlBlogRepository::class);
        $this->responder = $this->createMock(GetAllBlogResponder::class);
        $this->service = new GetAllBlogService($this->blogRepository, $this->responder);
        $this->validator = $this->createMock(GetAllBlogValidator::class);
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
    public function testExecuteReturnsBlogsWhenSelfUserHasPermission(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $this->validator->method('getUserId')->willReturn($userId);
        $userId->method('equals')->willReturn(true);
        $this->validator->method('withContent')->willReturn(false);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $blogs = $this->createMock(BlogPostCollection::class);
        $this->blogRepository->method('findBlogsFromOwner')
            ->with($userId)
            ->willReturn($blogs);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($blogs, false)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
