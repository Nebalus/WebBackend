<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\Click;

use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;

class ClickReferralView
{
    public static function render(Referral $referral): ResultInterface
    {
        $fields = [
            "url" => $referral->getUrl()->asString()
        ];

        return Result::createSuccess("Referral found", 200, $fields);
    }
}
