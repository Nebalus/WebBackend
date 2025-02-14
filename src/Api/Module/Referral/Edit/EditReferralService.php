<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\User\User;

readonly class EditReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    public function execute(EditReferralValidator $validator, User $user): ResultInterface
    {
        $this->referralRepository->updateReferral();

        return EditReferralView::render();
    }
}
