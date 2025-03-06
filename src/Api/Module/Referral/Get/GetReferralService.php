<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\Referral\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\User\User;

readonly class GetReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(GetReferralValidator $validator, User $user): ResultInterface
    {
        $referral = $this->referralRepository->findReferralByCodeFromOwner($user->getUserId(), $validator->getReferralCode());

        if ($referral === null) {
            return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return GetReferralView::render($referral);
    }
}
