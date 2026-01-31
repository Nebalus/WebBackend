<?php

namespace Nebalus\Webapi\Api\Module\Blog\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\BlogRepository\MySqlBlogRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Blog\BlogContent;
use Nebalus\Webapi\Value\Module\Blog\BlogExcerpt;
use Nebalus\Webapi\Value\Module\Blog\BlogSlug;
use Nebalus\Webapi\Value\Module\Blog\BlogStatus;
use Nebalus\Webapi\Value\Module\Blog\BlogTitle;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;

readonly class EditBlogService
{
    public function __construct(
        private MySqlBlogRepository $blogRepository,
        private EditBlogResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(EditBlogValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OWN_EDIT, true))) {
            return $this->run(
                $requestingUser->getUserId(),
                $validator->getSlug(),
                $validator->getImageBannerId(),
                $validator->getTitle(),
                $validator->getContent(),
                $validator->getExcerpt(),
                $validator->getStatus(),
                $validator->isFeatured()
            );
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_BLOG_OTHER_EDIT, true))) {
            return $this->run(
                $validator->getUserId(),
                $validator->getSlug(),
                $validator->getImageBannerId(),
                $validator->getTitle(),
                $validator->getContent(),
                $validator->getExcerpt(),
                $validator->getStatus(),
                $validator->isFeatured()
            );
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(
        UserId $ownerId,
        BlogSlug $slug,
        ?int $imageBannerId,
        BlogTitle $title,
        BlogContent $content,
        BlogExcerpt $excerpt,
        BlogStatus $status,
        bool $isFeatured
    ): ResultInterface {
        $blog = $this->blogRepository->updateBlogFromOwner($ownerId, $slug, $imageBannerId, $title, $content, $excerpt, $status, $isFeatured);
        if ($blog === null || !$ownerId->equals($blog->getOwnerId())) {
            return Result::createError('Blog does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }
        return $this->responder->render($blog);
    }
}