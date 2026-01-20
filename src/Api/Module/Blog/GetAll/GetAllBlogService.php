<?php

namespace Nebalus\Webapi\Api\Module\Blog\GetAll;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;

readonly class GetAllBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private GetAllBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetAllBlogValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OWN, true))) {
            return $this->run($requestingUser->getUserId());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OTHER, true))) {
            return $this->run($validator->getUserId());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $ownerId): ResultInterface
    {
        $blogs = $this->blogRepository->findBlogsFromOwner($ownerId);
        return $this->responder->render($blogs);
    }
}