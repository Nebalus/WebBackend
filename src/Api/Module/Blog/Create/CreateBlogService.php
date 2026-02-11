<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Create;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;

readonly class CreateBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private CreateBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(CreateBlogValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OWN_CREATE, true))) {
            $this->blogRepository->insertBlog(
                $requestingUser->getUserId(),
                $validator->getSlug(),
                $validator->getImageBannerId(),
                $validator->getTitle(),
                $validator->getContent(),
                $validator->getExcerpt(),
                $validator->getStatus(),
                $validator->isFeatured()
            );

            $blogId = $this->blogRepository->getLastInsertedBlogId();
            $blog = $this->blogRepository->findBlogById($blogId);

            return $this->responder->render($blog);
        }

        return ResultBuilder::buildNoPermissionResult();
    }
}
