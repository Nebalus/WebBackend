<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
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

readonly class ClickHistoryReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
        private ClickHistoryReferralResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(ClickHistoryReferralValidator $validator, User $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if ($isSelfUser && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OWN, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getReferralCode(), $validator->getRange());
        }

        if ($isSelfUser === false && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OTHER, true))) {
            return $this->run($validator->getUserId(), $validator->getReferralCode(), $validator->getRange());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $userId, ReferralCode $code, int $range): ResultInterface
    {
        $referral = $this->referralRepository->findReferralByCode($code);
        if ($referral === null || !$userId->equals($referral->getOwnerId())) {
            return Result::createError("Referral not found", StatusCodeInterface::STATUS_NOT_FOUND);
        }
        $data = $this->referralRepository->getReferralClicksFromRange($userId, $code, $range);
        return $this->responder->render($code, $data);
    }
}
