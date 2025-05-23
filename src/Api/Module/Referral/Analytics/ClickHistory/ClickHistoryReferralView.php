<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClick;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClicks;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;

class ClickHistoryReferralView
{
    public static function render(ReferralCode $referralCode, ReferralClicks $referralClicks): ResultInterface
    {
        $history = [];
        foreach ($referralClicks as $referralClick) {
            if ($referralClick instanceof ReferralClick === false) {
                continue;
            }

            $history[] = [
                "date" => $referralClick->getClickedAtDate()->format("Y-m-d"),
                "count" => $referralClick->getClickCount()->asInt(),
                "unique_visitors" => $referralClick->getUniqueVisitorsCount()->asInt(),
            ];
        }

        $fields = [
            "code" => $referralCode->asString(),
            "history" => $history,
        ];

        return Result::createSuccess("Referral history found", StatusCodeInterface::STATUS_OK, $fields);
    }
}
