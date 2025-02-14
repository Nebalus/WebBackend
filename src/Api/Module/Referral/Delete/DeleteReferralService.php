<?php

namespace Nebalus\Webapi\Api\Module\Referral\Delete;

use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\User\User;

readonly class DeleteReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    public function execute(DeleteReferralValidator $validator, User $user): ResultInterface
    {
        if ($this->referralRepository->deleteReferralByCodeAndOwnerId($validator->getReferralCode(), $user->getUserId())) {
            return DeleteReferralView::render();
        }

        return Result::createError('Referral does not exist', 404);
    }
}
