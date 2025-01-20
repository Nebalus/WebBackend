<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Value\Account\User\User;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

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
