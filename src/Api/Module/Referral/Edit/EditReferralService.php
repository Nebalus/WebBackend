<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodesTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\User;

readonly class EditReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
        private EditReferralResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(EditReferralValidator $validator, User $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($validator->getUserId() === $requestingUser->getUserId() && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OWN_EDIT, true))) {
            $updatedReferral = $this->referralRepository->updateReferralFromOwner($requestingUser->getUserId(), $validator->getCode(), $validator->getUrl(), $validator->getLabel(), $validator->isDisabled());
        }

        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OTHER_EDIT, true))) {
        }

        if ($updatedReferral instanceof Referral) {
            return $this->responder->render($updatedReferral);
        }

        return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
