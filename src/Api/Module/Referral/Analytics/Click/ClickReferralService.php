<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\Referral\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

readonly class ClickReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(ClickReferralValidator $validator): ResultInterface
    {
        $referral = $this->referralRepository->findReferralByCode($validator->getReferralCode());

        if (empty($referral) || $referral->isDisabled()) {
            return Result::createError("Referral code not found", StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->referralRepository->insertReferralClickEntry($referral->getReferralId());

        return ClickReferralView::render($referral);
    }
}
