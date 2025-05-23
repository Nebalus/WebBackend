<?php

namespace Nebalus\Webapi\Api\Module\Referral\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;

class CreateReferralView
{
    public static function render(Referral $referral): ResultInterface
    {
        $fields = [
            "code" => $referral->getCode()->asString(),
            "url" => $referral->getUrl()->asString(),
            "label" => $referral->getLabel()->asString(),
            "disabled" => $referral->isDisabled(),
            "created_at" => $referral->getCreatedAtDate()->format(DATE_ATOM),
            "updated_at" => $referral->getUpdatedAtDate()->format(DATE_ATOM),
        ];

        return Result::createSuccess("Referral created", StatusCodeInterface::STATUS_CREATED, $fields);
    }
}
