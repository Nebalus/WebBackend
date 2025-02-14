<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\Referral\Referral;

class EditReferralView
{
    public static function render(Referral $referral): ResultInterface
    {
        $fields = [
            "referral_id" => $referral->getReferralId()->asInt(),
            "owner_user_id" => $referral->getOwnerUserId()->asInt(),
            "code" => $referral->getCode()->asString(),
            "pointer" => $referral->getPointer()->asString(),
            "name" => $referral->getName()->asString(),
            "disabled" => $referral->isDisabled(),
            "created_at_timestamp" => $referral->getCreatedAtDate()->getTimestamp(),
            "updated_at_timestamp" => $referral->getUpdatedAtDate()->getTimestamp(),
        ];

        return Result::createSuccess("Referral edited", 200, $fields);
    }
}
