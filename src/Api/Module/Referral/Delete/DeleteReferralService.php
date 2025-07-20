<?php

namespace Nebalus\Webapi\Api\Module\Referral\Delete;

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

readonly class DeleteReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
        private DeleteReferralResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(DeleteReferralValidator $validator, User $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($validator->getUserId()->asInt() === $requestingUser->getUserId()->asInt() && $userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OWN, true))) {
            return $this->run($requestingUser->getUserId(), $validator->getReferralCode());
        }

        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodesTypes::FEATURE_REFERRAL_OTHER, true))) {
            return $this->run($validator->getUserId(), $validator->getReferralCode());
        }

        return ResultBuilder::buildNoPermissionResult();
    }

    private function run(UserId $userId, ReferralCode $code): ResultInterface
    {
        if ($this->referralRepository->deleteReferralByCodeFromOwner($userId, $code)) {
            return $this->responder->render();
        }
        return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
