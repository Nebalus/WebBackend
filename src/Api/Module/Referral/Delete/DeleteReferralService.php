<?php

namespace Nebalus\Webapi\Api\Module\Referral\Delete;

use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultInterface;
use Nebalus\Webapi\Value\User\UserId;

readonly class DeleteReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    public function execute(DeleteReferralValidator $validator): ResultInterface
    {
        if ($this->referralRepository->deleteReferralByCodeAndOwnerId($validator->getReferralCode(), UserId::from(2))) {
            return DeleteReferralView::render();
        }

        return Result::createError('Referral does not exist', 404);
    }
}