<?php

namespace Nebalus\Webapi\Api\Module\Referral\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCollection;
use Nebalus\Webapi\Value\Result\Result;

class GetAllReferralResponder
{
    public function render(ReferralCollection $referrals): ResultInterface
    {
        $fields = [];
        foreach ($referrals as $referral) {
            $fields[] = [
                "code" => $referral->getCode()->asString(),
                "url" => $referral->getUrl()->asString(),
                "label" => $referral->getLabel()->asString(),
                "disabled" => $referral->isDisabled(),
                "created_at" => $referral->getCreatedAtDate()->format(DATE_ATOM),
                "updated_at" => $referral->getUpdatedAtDate()->format(DATE_ATOM),
            ];
        }

        return Result::createSuccess("List of referrals found", StatusCodeInterface::STATUS_OK, $fields);
    }
}
