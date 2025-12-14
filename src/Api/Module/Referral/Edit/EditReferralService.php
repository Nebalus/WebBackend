<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\User;
use Nebalus\Webapi\Value\User\UserId;

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
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OWN_EDIT, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getCode(), $validator->getUrl(), $validator->getLabel(), $validator->isDisabled());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OTHER_EDIT, true))) {
            return $this->run($validator->getUserId(), $validator->getCode(), $validator->getUrl(), $validator->getLabel(), $validator->isDisabled());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $ownerId, ReferralCode $code, Url $url, ReferralLabel $label, bool $disabled): ResultInterface
    {
        $referral = $this->referralRepository->updateReferralFromOwner($ownerId, $code, $url, $label, $disabled);
        if ($referral === null || !$ownerId->equals($referral->getOwnerId())) {
            return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }
        return $this->responder->render($referral);
    }
}
