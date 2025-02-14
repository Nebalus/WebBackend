<?php

namespace Nebalus\Webapi\Api\Module\Referral\Create;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\Referral\ReferralCode;
use Nebalus\Webapi\Value\User\User;

readonly class CreateReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(CreateReferralValidator $validator, User $user): ResultInterface
    {
        $referralCode = ReferralCode::create();

        $this->referralRepository->insertReferral($user->getUserId(), $referralCode, $validator->getReferralPointer(), $validator->getReferralName(), $validator->isDisabled());

        $referral = $this->referralRepository->findReferralByCodeAndOwnerId($referralCode, $user->getUserId());

        return CreateReferralView::render($referral);
    }
}
