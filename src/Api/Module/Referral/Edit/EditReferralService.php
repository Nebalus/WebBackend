<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\Referral\MySqlReferralRepository;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\User\User;

readonly class EditReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(EditReferralValidator $validator, User $user): ResultInterface
    {
        $updatedReferral = $this->referralRepository->updateReferralFromOwner($user->getUserId(), $validator->getReferralCode(), $validator->getUrl(), $validator->getName(), $validator->isDisabled());

        if ($updatedReferral instanceof Referral) {
            return EditReferralView::render($updatedReferral);
        }

        return Result::createError('Referral does not exist', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
