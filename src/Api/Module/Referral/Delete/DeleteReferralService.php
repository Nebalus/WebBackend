<?php

namespace Nebalus\Webapi\Api\Module\Referral\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Repository\Referral\MySqlReferralRepository;
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
        if ($this->referralRepository->deleteReferralByCodeFromOwner($user->getUserId(), $validator->getReferralCode())) {
            return DeleteReferralView::render();
        }

        return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
