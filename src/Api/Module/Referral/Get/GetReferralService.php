<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodesTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\User;
use Nebalus\Webapi\Value\User\UserId;

readonly class GetReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
        private GetReferralResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetReferralValidator $validator, User $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->asInt() === $requestingUser->getUserId()->asInt();

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OWN, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getReferralCode());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OTHER, true))) {
            return $this->run($validator->getUserId(), $validator->getReferralCode());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $ownerId, ReferralCode $code): ResultInterface
    {
        $referral = $this->referralRepository->findReferralByCodeFromOwner($ownerId, $code);
        if ($referral === null) {
            return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }
        return $this->responder->render($referral);
    }
}
