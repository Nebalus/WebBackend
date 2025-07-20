<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodesTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Range;
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
        if ($validator->getUserId()->asInt() === $requestingUser->getUserId()->asInt() && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OWN, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getReferralCode(), $validator->getRange());
        }

        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OTHER, true))) {
            return $this->run($validator->getUserId(), $validator->getReferralCode(), $validator->getRange());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    /**
     * @throws ApiException
     */
    private function run(UserId $userId, ReferralCode $code, int $range): ResultInterface
    {
        $referral = $this->referralRepository->findReferralByCodeFromOwner($userId, $code);
        if ($referral === null) {
            return Result::createError("Referral not found");
        }
        $data = $this->referralRepository->getReferralClicksFromRange($userId, $code, $range);
        return $this->responder->render($code, $data);
    }
}
