<?php

namespace Nebalus\Webapi\Api\Module\Blog\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogId;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;

readonly class DeleteBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private DeleteBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(DeleteBlogValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OWN_DELETE, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getBlogId());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OTHER_DELETE, true))) {
            return $this->run($validator->getUserId(), $validator->getBlogId());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    private function run(UserId $userId, BlogId $blogId): ResultInterface
    {
        if ($this->blogRepository->deleteBlogByIdFromOwner($userId, $blogId)) {
            return $this->responder->render();
        }
        return Result::createError('Blog does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}