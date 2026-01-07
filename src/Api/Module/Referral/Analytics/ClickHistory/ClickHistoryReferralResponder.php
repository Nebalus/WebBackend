<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClick;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClickCollection;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Result\Result;

class ClickHistoryReferralResponder
{
    public function render(ReferralCode $referralCode, ReferralClickCollection $referralClicks): ResultInterface
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
