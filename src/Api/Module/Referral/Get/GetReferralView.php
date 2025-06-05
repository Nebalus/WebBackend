<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Internal\Result;
use Nebalus\Webapi\Value\Module\Referral\Referral;

class GetReferralView
{
    public function render(Referral $referral): ResultInterface
    {
        $fields = [
            "code" => $referral->getCode()->asString(),
            "url" => $referral->getUrl()->asString(),
            "label" => $referral->getLabel()->asString(),
            "disabled" => $referral->isDisabled(),
            "created_at" => $referral->getCreatedAtDate()->format(DATE_ATOM),
            "updated_at" => $referral->getUpdatedAtDate()->format(DATE_ATOM),
        ];

        return Result::createSuccess("Referral fetched", StatusCodeInterface::STATUS_OK, $fields);
    }
}
