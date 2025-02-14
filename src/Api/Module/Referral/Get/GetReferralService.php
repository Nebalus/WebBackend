<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
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
        $referral = $this->referralRepository->findReferralByCodeAndOwnerId($validator->getReferralCode(), $user->getUserId());

        if ($referral === null) {
            return Result::createError('Referral does not exist', 404);
        }

        return GetReferralView::render($referral);
    }
}
