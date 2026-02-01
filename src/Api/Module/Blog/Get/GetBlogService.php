<?php

namespace Nebalus\Webapi\Api\Module\Blog\Get;

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

readonly class GetBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private GetBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetBlogValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OWN, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getBlogId(), $validator->withContent());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OTHER, true))) {
            return $this->run($validator->getUserId(), $validator->getBlogId(), $validator->withContent());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $ownerId, BlogId $blogId, bool $withContent): ResultInterface
    {
        $blog = $this->blogRepository->findBlogById($blogId);
        if ($blog === null || !$ownerId->equals($blog->getOwnerId())) {
            return Result::createError('Blog does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }
        return $this->responder->render($blog, $withContent);
    }
}
