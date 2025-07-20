<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\User;

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
        $referral = $this->referralRepository->findReferralByCodeFromOwner($requestingUser->getUserId(), $validator->getReferralCode());

        if ($referral === null) {
            return Result::createError('Referral not found', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $this->responder->render($referral);
    }
}
