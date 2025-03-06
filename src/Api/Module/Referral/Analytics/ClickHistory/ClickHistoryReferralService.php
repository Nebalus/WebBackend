<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\Referral\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\User\User;

readonly class ClickHistoryReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(ClickHistoryReferralValidator $validator, User $user): ResultInterface
    {
        $data = $this->referralRepository->getReferralClicksFromRange($user->getUserId(), $validator->getReferralCode(), $validator->getRange());

        return ClickHistoryReferralView::render($validator->getReferralCode(), $data);
    }
}
