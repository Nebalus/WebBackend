<?php

namespace Nebalus\Webapi\Api\Module\Referral\GetAll;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\User;
use Nebalus\Webapi\Value\User\UserId;

readonly class GetAllReferralService
{
    public function __construct(
        private MySqlReferralRepository $referralRepository,
        private GetAllReferralResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetAllReferralValidator $validator, User $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OWN, true))) {
            return $this->run($requestingUser->getUserId());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OTHER, true))) {
            return $this->run($validator->getUserId());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $ownerId): ResultInterface
    {
        $referrals = $this->referralRepository->findReferralsFromOwner($ownerId);
        return $this->responder->render($referrals);
    }
}
