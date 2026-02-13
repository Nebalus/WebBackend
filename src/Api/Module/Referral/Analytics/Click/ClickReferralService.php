<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Utils\CryptUtils;
use Nebalus\Webapi\Utils\IpUtils;
use Nebalus\Webapi\Value\Hash\SHA256Hash;
use Nebalus\Webapi\Value\Result\Result;

readonly class ClickReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
        private ClickReferralResponder $responder
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(ClickReferralValidator $validator, SHA256Hash $anonymousIdentityHash): ResultInterface
    {
        $referral = $this->referralRepository->findReferralByCode($validator->getReferralCode());

        if (empty($referral) || $referral->isDisabled()) {
            return Result::createError("Referral code not found", StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->referralRepository->insertReferralClickEntry($referral->getReferralId(), $anonymousIdentityHash);

        return $this->responder->render($referral);
    }
}
